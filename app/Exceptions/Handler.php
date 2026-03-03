<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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

        // REGISTRA AUTOMATICAMENTE TENTATIVAS DE ACESSO NEGADO (403)
        $this->renderable(function (\Throwable $e, $request) {
            // Se for qualquer erro 403 ou as classes específicas de acesso negado
            if (method_exists($e, 'getStatusCode') && $e->getStatusCode() === 403) {
                if (auth()->check()) {
                    \App\Models\SecurityLog::create([
                        'user_id' => auth()->id(),
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'type' => 'unauthorized_access'
                    ]);
                }
            }
        });
    }
}
