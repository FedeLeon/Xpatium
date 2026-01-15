<?php

namespace App\Exceptions;

use App\Models\ErrorLog;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e): void {
            if (app()->runningInConsole()) {
                return;
            }

            if ($e instanceof ValidationException) {
                return;
            }

            $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : null;
            if ($statusCode !== null && $statusCode < 500 && $statusCode !== 404) {
                return;
            }

            try {
                ErrorLog::create([
                    'level' => 'error',
                    'exception_class' => $e::class,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'status_code' => $statusCode,
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'user_id' => Auth::id(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'trace' => $e->getTraceAsString(),
                ]);
            } catch (Throwable $loggingException) {
                Log::error('Error al guardar error_log', [
                    'error' => $loggingException->getMessage(),
                ]);
            }
        });
    }
}
