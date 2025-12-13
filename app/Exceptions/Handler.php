<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    // …

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            try {
                storeLogs('exception', json_encode([
                    'message'   => $e->getMessage(),
                    'exception' => get_class($e),
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                    'url'       => request()->fullUrl(),
                    'input'     => request()->except(['password','password_confirmation']),
                    'user_id'   => auth()->id(),
                ]));
            } catch (Throwable $loggingError) {
                // if DB is down or the insert fails, fall back to Laravel’s default log
                \Log::error('Failed to write to errors_log table', [
                    'error'     => $loggingError->getMessage(),
                    'original'  => (string) $e,
                ]);
            }
        });
    }
}
