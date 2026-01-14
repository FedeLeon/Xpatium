<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('student.profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'avatar' => ['nullable', 'image', 'max:2048'],
            'name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $updates = [];

        if (! empty($data['name'])) {
            $updates['name'] = $data['name'];
        }
        if (array_key_exists('last_name', $data)) {
            $updates['last_name'] = $data['last_name'] ?: null;
        }
        if (! empty($data['email'])) {
            $updates['email'] = $data['email'];
        }

        foreach (['birth_date', 'phone', 'address', 'country', 'state', 'city', 'postal_code'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = $data[$field] ?: null;
            }
        }

        if (! empty($data['password'])) {
            $updates['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $updates['avatar_path'] = $request->file('avatar')->store('avatars/'.$user->id, 'public');
        }

        if (! empty($updates)) {
            $user->update($updates);
        }

        return back()->with('status', 'Perfil actualizado');
    }
}
