<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $announcements = Announcement::where('teacher_id', $request->user()->id)
            ->latest()
            ->get();

        return view('teacher.announcements.index', compact('announcements'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcements', 'public');
        }

        Announcement::create([
            'teacher_id' => $request->user()->id,
            'title' => $data['title'],
            'body' => $data['body'],
            'image_path' => $imagePath,
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'status' => Announcement::STATUS_ACTIVE,
        ]);

        return redirect()->route('teacher.announcements.index');
    }

    public function updateStatus(Request $request, Announcement $announcement): RedirectResponse
    {
        if ($announcement->teacher_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate([
            'status' => ['required', 'in:activo,inactivo'],
        ]);

        $announcement->status = $data['status'];
        $announcement->save();

        return redirect()->route('teacher.announcements.index');
    }

    public function destroy(Request $request, Announcement $announcement): RedirectResponse
    {
        if ($announcement->teacher_id !== $request->user()->id) {
            abort(404);
        }

        if ($announcement->image_path) {
            Storage::disk('public')->delete($announcement->image_path);
        }

        $announcement->delete();

        return redirect()->route('teacher.announcements.index');
    }
}
