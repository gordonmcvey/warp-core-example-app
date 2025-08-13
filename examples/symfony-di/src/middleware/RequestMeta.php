<?php

/**
 * Copyright © 2025 Gordon McVey
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
