<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(Request $request): View
    {
        $assets = Asset::where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->groupBy('type');

        return view('teacher.assets.index', compact('assets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:video,image,file'],
            'file' => ['required', 'file', 'max:51200'],
        ]);

        if ($data['type'] === 'image') {
            $request->validate([
                'file' => ['image', 'max:4096'],
            ]);
        }

        $file = $request->file('file');
        $path = $file->store('assets/'.$request->user()->id.'/'.$data['type'], 'public');

        Asset::create([
            'user_id' => $request->user()->id,
            'type' => $data['type'],
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        return redirect()->route('teacher.assets.index');
    }
}
