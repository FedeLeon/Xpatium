<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Credenciales invalidas.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return $this->redirectToDashboard($request->user());
    }

    public function showRegister(): View
    {
        if (! session('register_role')) {
            return redirect()->route('register');
        }

        return view('auth.register');
    }

    public function showRegisterRole(): View
    {
        return view('auth.register-role');
    }

    public function storeRegisterRole(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:teacher,student'],
        ]);

        $request->session()->put('register_role', $data['role']);

        return redirect()->route('register.form');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $role = $request->session()->pull('register_role');
        if (! in_array($role, [User::ROLE_TEACHER, User::ROLE_STUDENT], true)) {
            return redirect()->route('register');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectToDashboard($user);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function redirectToDashboard(User $user): RedirectResponse
    {
        return match ($user->role) {
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            User::ROLE_TEACHER => redirect()->route('teacher.dashboard'),
            User::ROLE_STUDENT => redirect()->route('student.dashboard'),
            default => redirect()->route('home'),
        };
    }
}
