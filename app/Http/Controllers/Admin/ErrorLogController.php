<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use Illuminate\View\View;

class ErrorLogController extends Controller
{
    public function index(): View
    {
        $logs = ErrorLog::latest()->paginate(20);

        return view('admin.errors.index', compact('logs'));
    }

    public function show(ErrorLog $errorLog): View
    {
        return view('admin.errors.show', compact('errorLog'));
    }
}
