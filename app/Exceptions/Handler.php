<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Exception\NotFoundException;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ],403);
        });

        $this->renderable(function (PermissionDoesNotExist $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ],404);
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ],404);
        });
    }



    
}