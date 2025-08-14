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

namespace gordonmcvey\exampleapp\controller\Health;

use gordonmcvey\exampleapp\middleware\RequestMeta;
use gordonmcvey\httpsupport\enum\statuscodes\ClientErrorCodes;
use gordonmcvey\httpsupport\enum\statuscodes\SuccessCodes;
use gordonmcvey\httpsupport\enum\Verbs;
use gordonmcvey\httpsupport\interface\request\RequestInterface;
use gordonmcvey\httpsupport\interface\response\ResponseInterface;
use gordonmcvey\httpsupport\response\Response;
use gordonmcvey\WarpCore\Exceptions\Routing;
use gordonmcvey\WarpCore\interface\controller\RequestHandlerInterface;
use JsonException;

final readonly class EchoPayload implements RequestHandlerInterface
{
    private const array ALLOWED_METHODS = [
        Verbs::POST,
        Verbs::PUT,
    ];

    /**
     * @throws Routing
     */
    public function dispatch(RequestInterface $request): ResponseInterface
    {
        if (!in_array($request->verb(), self::ALLOWED_METHODS)) {
            // @todo We need more exception types
            throw new Routing("Method not allowed", ClientErrorCodes::METHOD_NOT_ALLOWED->value);
        }

        try {
            $payload = json_decode($request->body(), flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Routing("Payload is not valid JSON", ClientErrorCodes::BAD_REQUEST->value, $e);
        }

        return new Response(
            responseCode: SuccessCodes::OK,
            body: (string) json_encode([
                "requestId" => $request->header(RequestMeta::HEADER_REQUEST_ID, "unknown"),
                "received"  => $request->header(RequestMeta::HEADER_RECEIVED, "unknown"),
                "payload"   => $payload,
            ]),
            contentType: "application/json",
            encoding: "utf-8",
        );
    }
}
