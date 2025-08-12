<?php

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
use gordonmcvey\WarpCore\interface\middleware\MiddlewareProviderInterface;
use gordonmcvey\WarpCore\middleware\MiddlewareProviderTrait;

final class Ping implements RequestHandlerInterface, MiddlewareProviderInterface
{
    use MiddlewareProviderTrait;

    private const array ALLOWED_METHODS = [
        Verbs::GET,
        Verbs::HEAD,
    ];

    /**
     * @throws Routing
     */
    public function dispatch(RequestInterface $request): ?ResponseInterface
    {
        if (!in_array($request->verb(), self::ALLOWED_METHODS)) {
            // @todo We need more exception types
            throw new Routing("Method not allowed", ClientErrorCodes::METHOD_NOT_ALLOWED->value);
        }

        return new Response(
            responseCode: SuccessCodes::OK,
            body: (string) json_encode([
                "requestId" => $request->header(RequestMeta::HEADER_REQUEST_ID, "unknown"),
                'healthy'   => true,
                "received"  => $request->header(RequestMeta::HEADER_RECEIVED, "unknown"),
                "processed" => "unknown",
            ]),
            contentType: 'application/json',
            encoding: 'utf-8',
        );
    }
}
