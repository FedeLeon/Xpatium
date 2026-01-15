<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e): void {
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
                \App\Models\ErrorLog::create([
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
    })->create();
