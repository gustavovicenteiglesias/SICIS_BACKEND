<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Support\Api\ApiErrorResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append([
            \App\Http\Middleware\AttachRequestId::class,
        ]);

        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json(
                ApiErrorResponse::make(
                    $request,
                    'Debes autenticarte para acceder a este recurso.',
                    401,
                    null,
                    'AUTH_REQUIRED'
                ),
                401
            )->header('X-Request-Id', (string) $request->attributes->get('request_id'));
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            return response()->json(
                ApiErrorResponse::make(
                    $request,
                    $e->getMessage() ?: 'No tenes permisos para realizar esta accion.',
                    403,
                    null,
                    'FORBIDDEN'
                ),
                403
            )->header('X-Request-Id', (string) $request->attributes->get('request_id'));
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json(
                ApiErrorResponse::make(
                    $request,
                    'Los datos enviados no pasaron la validacion.',
                    422,
                    $e->errors(),
                    'VALIDATION_ERROR'
                ),
                422
            )->header('X-Request-Id', (string) $request->attributes->get('request_id'));
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            return response()->json(
                ApiErrorResponse::make(
                    $request,
                    'No se encontro el recurso solicitado.',
                    404,
                    null,
                    'NOT_FOUND'
                ),
                404
            )->header('X-Request-Id', (string) $request->attributes->get('request_id'));
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) {
                $status = $e->getStatusCode();
                $code = match ($status) {
                    401 => 'AUTH_REQUIRED',
                    403 => 'FORBIDDEN',
                    404 => 'NOT_FOUND',
                    422 => 'VALIDATION_ERROR',
                    default => 'HTTP_ERROR',
                };

                return response()->json(
                    ApiErrorResponse::make(
                        $request,
                        $e->getMessage() ?: 'Ocurrio un error al procesar la solicitud.',
                        $status,
                        null,
                        $code
                    ),
                    $status
                )->header('X-Request-Id', (string) $request->attributes->get('request_id'));
            }

            return response()->json(
                ApiErrorResponse::make(
                    $request,
                    'Ocurrio un error interno. Si el problema persiste, revisar logs con el request_id.',
                    500,
                    null,
                    'INTERNAL_ERROR'
                ),
                500
            )->header('X-Request-Id', (string) $request->attributes->get('request_id'));
        });

        $exceptions->report(function (Throwable $e) {
            if ($e instanceof ValidationException || $e instanceof AuthenticationException || $e instanceof AuthorizationException || $e instanceof ModelNotFoundException) {
                return false;
            }

            $request = app()->bound('request') ? app('request') : null;

            Log::error('API exception', [
                'request_id' => $request?->attributes->get('request_id'),
                'method' => $request?->method(),
                'path' => $request?->path(),
                'usuario_id' => $request?->user()?->id,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return false;
        });
    })->create();
