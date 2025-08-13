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
