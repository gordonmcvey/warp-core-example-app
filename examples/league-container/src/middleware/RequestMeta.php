<?php

declare(strict_types=1);

namespace gordonmcvey\exampleapp\middleware;

use DateTimeInterface;
use gordonmcvey\httpsupport\interface\request\RequestInterface;
use gordonmcvey\httpsupport\interface\response\ResponseInterface;
use gordonmcvey\WarpCore\interface\controller\RequestHandlerInterface;
use gordonmcvey\WarpCore\interface\middleware\MiddlewareInterface;

final readonly class RequestMeta implements MiddlewareInterface
{
    public const string HEADER_RECEIVED = "X-Request-Received";
    public const string HEADER_REQUEST_ID = "X-Request-ID";
    private const string TIMESTAMP_FORMAT = "Y-m-d\TH:i:s.uP";

    private string $requestId;

    public function __construct(private DateTimeInterface $received)
    {
        // @todo Make this a dependency (UUID, ULID, etc)
        $this->requestId = uniqid(more_entropy: true);
    }

    public function handle(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Adding the metadata to the request headers will make it available in the call stack
        $request
            ->setHeader(self::HEADER_RECEIVED, $this->received->format(self::TIMESTAMP_FORMAT))
            ->setHeader(self::HEADER_REQUEST_ID, $this->requestId)
        ;

        return $handler->dispatch($request)
            ->setHeader(self::HEADER_RECEIVED, $this->received->format(self::TIMESTAMP_FORMAT))
            ->setHeader(self::HEADER_REQUEST_ID, $this->requestId)
        ;
    }
}
