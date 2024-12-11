<?php

namespace App\Exceptions;

use ErrorException;
use BadMethodCallException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request as HttpRequest;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
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
        $this->renderable(function (NotFoundHttpException $e, HttpRequest $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => str_contains($e->getMessage(), 'The route') ? 'Endpoint not found. If error persists, contact ' . config('app.name') . ' customer care.' : (str_contains($e->getMessage(), 'No query results') ? str_replace(']', '', last(explode('\\', $e->getMessage()))) . ' not found.' : $e->getMessage()),
                ], 404);
            }
        });

        $this->renderable(function (ServiceUnavailableHttpException $e, HttpRequest $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Server Error. If error persists, contact ' . config('app.name') . ' customer care.'
                ], 503);
            }
        });

        $this->renderable(function (BadRequestHttpException $e, HttpRequest $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request'
                ], 400);
            }
        });

        $this->renderable(function (ErrorException $e, HttpRequest $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get service'
                ], 500);
            }
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, HttpRequest $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'The method is not supported for this route.'
                ], 405);
            }
        });

        $this->renderable(function (BadMethodCallException $e, HttpRequest $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request. If error persists, contact ' . config('app.name') . ' customer care.'
                ], 400);
            }
        });
    }
}
