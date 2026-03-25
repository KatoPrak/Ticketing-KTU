<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // app/Exceptions/Handler.php
public function render($request, Throwable $exception)
{
    // Tangani error 419 (Page Expired / Token Mismatch)
    if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Session expired. Please refresh the page.'], 419);
        }
        return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir, silakan login kembali.');
    }

    if ($request->expectsJson()) {
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
    }
    return parent::render($request, $exception);
}

}
