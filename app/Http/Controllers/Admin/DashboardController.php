<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $teachers = User::where('role', User::ROLE_TEACHER)
            ->withSum('assets', 'size')
            ->orderBy('name')
            ->get();

        $planOptions = User::storagePlans();

        return view('dashboards.admin', compact('teachers', 'planOptions'));
    }

    public function updateTeacherPlan(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== User::ROLE_TEACHER) {
            abort(404);
        }

        $validPlans = array_keys(User::storagePlans());

        $data = $request->validate([
            'storage_plan' => ['required', 'in:'.implode(',', $validPlans)],
        ]);

        $user->storage_plan = $data['storage_plan'];
        $user->save();

        return back();
    }
}
