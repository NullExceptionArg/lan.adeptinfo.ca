<?php
namespace App\Exceptions;

use Exception;
use Dingo\Api\Exception\Handler as DingoHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiExceptionsHandler extends DingoHandler
{
    public function handle(Exception $e)
    {
        if (env('APP_DEBUG')) {
            return parent::handle($e);
        }
        $success = false;
        $response = null;
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        if($e instanceof BadRequestHttpException) {
            $status = Response::HTTP_BAD_REQUEST;
            $e = new BadRequestHttpException($e->getMessage());
        } elseif ($e instanceof HttpResponseException) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $response = $e->getResponse();
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $status = Response::HTTP_METHOD_NOT_ALLOWED;
            $e = new MethodNotAllowedHttpException([], 'HTTP_METHOD_NOT_ALLOWED', $e);
        } elseif ($e instanceof NotFoundHttpException) {
            $status = Response::HTTP_NOT_FOUND;
            $e = new NotFoundHttpException('HTTP_NOT_FOUND', $e);
        } elseif ($e instanceof AuthorizationException) {
            $status = Response::HTTP_FORBIDDEN;
            $e = new AuthorizationException('HTTP_FORBIDDEN', $status);
        } elseif ($e instanceof \Dotenv\Exception\ValidationException && $e->getResponse()) {
            $status = Response::HTTP_BAD_REQUEST;
            $e = new \Dotenv\Exception\ValidationException('HTTP_BAD_REQUEST', $status, $e);
            $response = $e->getResponse();
        } elseif ($e) {
            $e = new HttpException($status, 'HTTP_INTERNAL_SERVER_ERROR');
        }
        return response()->json([
            'success' => $success,
            'status' => $status,
            'message' => json_decode($e->getMessage())
        ], $status);
    }
}