<?php

use App\Exceptions\AuthenticationException as AppAuthenticationException;
use App\Exceptions\UserNotFoundException;
use App\Http\Middleware\SetLocale;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'locale' => SetLocale::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // JWT Exceptions
        $exceptions->render(function (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.auth.token_expired'),
            ], 401);
        });

        $exceptions->render(function (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.auth.token_invalid'),
            ], 401);
        });

        $exceptions->render(function (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.auth.unauthorized'),
            ], 401);
        });

        // Custom Exceptions
        $exceptions->render(function (AppAuthenticationException $e) {
            return $e->render();
        });

        $exceptions->render(function (UserNotFoundException $e) {
            return $e->render();
        });

        // Validation Exception
        $exceptions->render(function (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.validation.name_required'),
                'errors' => $e->errors(),
            ], 422);
        });
    })->create();
