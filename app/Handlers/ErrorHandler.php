<?php

declare(strict_types=1);

namespace App\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

class ErrorHandler implements ErrorHandlerInterface
{
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        $statusCode = 500;
        $message = '服务器内部错误';

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $message = $exception->getMessage();
        } elseif ($displayErrorDetails) {
            $message = $exception->getMessage();
        }

        $payload = json_error($statusCode, $message);
        if ($displayErrorDetails) {
            $payload['trace'] = $exception->getTraceAsString();
        }

        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode($payload));
        return $response->withStatus($statusCode);
    }
}
