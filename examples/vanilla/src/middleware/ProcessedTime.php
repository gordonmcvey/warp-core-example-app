<?php

namespace gordonmcvey\exampleapp\middleware;

use DateTimeImmutable;
use gordonmcvey\httpsupport\interface\request\RequestInterface;
use gordonmcvey\httpsupport\interface\response\ResponseInterface;
use gordonmcvey\WarpCore\interface\controller\RequestHandlerInterface;
use gordonmcvey\WarpCore\interface\middleware\MiddlewareInterface;
use JsonException;

readonly final class ProcessedTime implements MiddlewareInterface
{
    private const string TIMESTAMP_FORMAT = "Y-m-d\TH:i:s.uP";

    /**
     * @throws JsonException
     */
    public function handle(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->dispatch($request);
        $responseBody = (object) json_decode(json: $response->body(), flags: JSON_THROW_ON_ERROR);
        $responseBody->processed = (new DateTimeImmutable())->format(self::TIMESTAMP_FORMAT);
        $response->setBody(json_encode($responseBody));

        return $response;
    }
}
