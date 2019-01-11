<?php

namespace App\Exceptions;

use Dingo\Api\Exception\Handler as DingoHandler;
use Exception;
use Illuminate\{Auth\Access\AuthorizationException, Http\Response};
use Symfony\{Component\HttpKernel\Exception\BadRequestHttpException,
    Component\HttpKernel\Exception\MethodNotAllowedHttpException,
    Component\HttpKernel\Exception\NotFoundHttpException};

class ApiExceptionsHandler extends DingoHandler
{
    public function handle(Exception $e)
    {
        $message = null;
        $status = null;
        switch (true) {
            case $e instanceof BadRequestHttpException:
                $status = Response::HTTP_BAD_REQUEST;
                $message = json_decode($e->getMessage());
                break;
            case $e instanceof MethodNotAllowedHttpException:
                $status = Response::HTTP_METHOD_NOT_ALLOWED;
                $message = $e->getMessage();
                break;
            case $e instanceof NotFoundHttpException:
                $status = Response::HTTP_NOT_FOUND;
                $message = $e->getMessage();
                break;
            case $e instanceof AuthorizationException:
                $status = Response::HTTP_FORBIDDEN;
                $message = $e->getMessage();
                break;
            default:
                $status = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (env('APP_DEBUG')) {
                    $message = $e->getMessage();
                }
        }

        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $message
        ], $status);
    }
}
