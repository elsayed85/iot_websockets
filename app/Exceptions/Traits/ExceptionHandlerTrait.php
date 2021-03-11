<?php

namespace App\Exceptions\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

trait ExceptionHandlerTrait
{
    public function handelException(Throwable $e)
    {
        switch (true) {
            case $e instanceof AuthorizationException:
                return $this->AuthorizationExceptionHandler($e);
                break;
            case $e instanceof MethodNotAllowedHttpException:
                return $this->MethodNotAllowedHttpExceptionHandler($e);
                break;
            case $e instanceof AuthorizationException:
                return $this->AuthorizationExceptionHandler($e);
                break;
            case $e instanceof AuthorizationException:
                return $this->AuthorizationExceptionHandler($e);
                break;
            case $e instanceof AuthorizationException:
                return $this->AuthorizationExceptionHandler($e);
                break;

            default:
                # code...
                break;
        }
    }

    public function AuthorizationExceptionHandler(Throwable $e)
    {
        return response()->json([
            'message' => $e->getMessage(),
            'success' => false,
            'code' => 401
        ],  401);
    }

    public function MethodNotAllowedHttpExceptionHandler(Throwable $e)
    {
        return response()->json([
            'message' => $e->getMessage(),
            'success' => false,
            'code' => 401
        ],  401);
    }
}
