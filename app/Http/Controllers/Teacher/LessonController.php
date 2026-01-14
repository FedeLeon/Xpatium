<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Lesson;
use App\Models\LessonContent;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function show(Request $request, Lesson $lesson): View
    {
        $course = $lesson->module->course;
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $lesson->load('contents.asset');
        $comments = $lesson->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->get();
        $assets = Asset::where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->groupBy('type');

        return view('teacher.lessons.show', compact('lesson', 'course', 'assets', 'comments'));
    }

    public function storeContent(Request $request, Lesson $lesson): RedirectResponse
    {
        $course = $lesson->module->course;
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate([
            'type' => ['required', 'in:text,video,image,file'],
            'asset_id' => ['nullable', 'integer'],
            'text_content' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:51200'],
        ]);

        if ($data['type'] === 'text') {
            $request->validate([
                'text_content' => ['required', 'string'],
            ]);
        }

        if ($data['type'] === 'image') {
            $request->validate([
                'file' => ['nullable', 'image', 'max:4096'],
            ]);
        }

        $assetId = null;
        if (! empty($data['asset_id'])) {
            $asset = Asset::where('id', $data['asset_id'])
                ->where('user_id', $request->user()->id)
                ->where('type', $data['type'])
                ->first();
            if (! $asset) {
                return back()->withErrors(['asset_id' => 'Archivo invalido.']);
            }
            $assetId = $asset->id;
        } elseif ($request->hasFile('file') && $data['type'] !== 'text') {
            $file = $request->file('file');
            $path = $file->store('assets/'.$request->user()->id.'/'.$data['type'], 'public');
            $mime = $file->getClientMimeType();
            $size = $file->getSize();

            if ($data['type'] === 'image') {
                $converted = ImageService::convertToWebp('public', $path, 'assets/'.$request->user()->id.'/image');
                $path = $converted['path'];
                $mime = $converted['mime'];
                $size = $converted['size'];
            }

            $asset = Asset::create([
                'user_id' => $request->user()->id,
                'type' => $data['type'],
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $mime,
                'size' => $size,
            ]);

            $thumbDir = 'assets/'.$request->user()->id.'/thumbs';
            if ($data['type'] === 'image') {
                $asset->thumb_path = ImageService::createImageThumbWebp('public', $asset->path, $thumbDir);
                $asset->save();
            } elseif ($data['type'] === 'video') {
                $asset->thumb_path = ImageService::createVideoThumbWebp('public', $asset->path, $thumbDir);
                $asset->save();
            }

            $assetId = $asset->id;
        } elseif ($data['type'] !== 'text') {
            return back()->withErrors(['asset_id' => 'Selecciona un archivo o sube uno nuevo.']);
        }

        LessonContent::create([
            'lesson_id' => $lesson->id,
            'type' => $data['type'],
            'asset_id' => $assetId,
            'text_content' => $data['type'] === 'text' ? $data['text_content'] : null,
        ]);

        return redirect()->route('teacher.lessons.show', $lesson);
    }
}
