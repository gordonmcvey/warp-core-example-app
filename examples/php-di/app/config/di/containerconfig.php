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

use gordonmcvey\exampleapp\controller\Health\EchoPayload;
use gordonmcvey\exampleapp\controller\Health\Ping;
use gordonmcvey\exampleapp\factory\DiControllerFactory;
use gordonmcvey\exampleapp\middleware\ProcessedTime;
use gordonmcvey\exampleapp\middleware\RequestMeta;
use gordonmcvey\httpsupport\enum\factory\StatusCodeFactory;
use gordonmcvey\httpsupport\enum\Verbs;
use gordonmcvey\httpsupport\interface\request\RequestInterface;
use gordonmcvey\httpsupport\interface\response\ResponseSenderInterface;
use gordonmcvey\httpsupport\request\payload\JsonPayloadHandler;
use gordonmcvey\httpsupport\request\Request;
use gordonmcvey\httpsupport\response\sender\ResponseSender;
use gordonmcvey\WarpCore\Bootstrap;
use gordonmcvey\WarpCore\error\JsonErrorHandler;
use gordonmcvey\WarpCore\FrontController;
use gordonmcvey\WarpCore\interface\controller\ControllerFactoryInterface;
use gordonmcvey\WarpCore\interface\error\ErrorHandlerInterface;
use gordonmcvey\WarpCore\interface\routing\RouterInterface;
use gordonmcvey\WarpCore\middleware\CallStackFactory;
use gordonmcvey\WarpCore\routing\RequestPathValidator;
use gordonmcvey\WarpCore\routing\Router;
use gordonmcvey\WarpCore\routing\StaticStrategy;
use Psr\Container\ContainerInterface;

/*
 * PHP-DI doesn't appear to support variadic parameter lists.  When I tried the below code the instances only got the
 * first of the parameters I listed.  I've implemented workarounds in the definitions but have preserved my original
 * attempt in the comment here in case PHP-DI is updated to support variadic parameters and I can drop this in when
 * that happens.
 *
 * "PingRoute"                       => DI\create(StaticStrategy::class)
 *     ->constructor(["/health/ping" => Ping::class], Verbs::GET, Verbs::HEAD),
 * "EchoPayloadRoute"                => DI\create(StaticStrategy::class)
 *     ->constructor(["/health/echo-payload" => EchoPayload::class], Verbs::POST, Verbs::PUT),
 * RouterInterface::class            => DI\create(Router::class)
 *     ->constructor(
 *         DI\get(RequestPathValidator::class),
 *         DI\get("PingRoute"),
 *         DI\get("EchoPayloadRoute"),
 *     ),
 * "PingRoute"                       => fn() => new StaticStrategy(["/health/ping" => Ping::class], Verbs::GET, Verbs::HEAD),
 */

return [
    // Attributes
    "jsonFlags"                       => DI\factory(fn() => !empty($_ENV["PRETTY_PRINT_JSON"]) ? JSON_PRETTY_PRINT : 0),
    "exposeErrorDetails"              => DI\factory(fn() => !empty($_ENV["DETAILED_ERROR_OUTPUT"])),

    // Core
    ResponseSenderInterface::class    => DI\create(ResponseSender::class),

    // HTTP request
    RequestInterface::class           => fn(ContainerInterface $container): Request
        => Request::fromSuperGlobals($container->get(JsonPayloadHandler::class)),

    // Error handlers
    ErrorHandlerInterface::class      => DI\create(JsonErrorHandler::class)
        ->constructor(
            DI\get(StatusCodeFactory::class),
            DI\get("jsonFlags"),
            DI\get("exposeErrorDetails"),
        ),

    // Middleware
    RequestMeta::class                => DI\create(RequestMeta::class)
        ->constructor(DI\get("received")),

    // Controllers
    ControllerFactoryInterface::class => DI\get(DiControllerFactory::class),
    Ping::class                       => DI\create(Ping::class)
        ->method("addMiddleware", DI\get(ProcessedTime::class)),

    // Routing
    RouterInterface::class            => fn() => new Router(
        new RequestPathValidator(),
        new StaticStrategy(["/health/ping" => Ping::class], Verbs::GET, Verbs::HEAD),
        new StaticStrategy(["/health/echo-payload" => EchoPayload::class], Verbs::POST, Verbs::PUT),
    ),

    // Front Controller
    Bootstrap::class                  => DI\create(Bootstrap::class)
        ->constructor(
            DI\get(RouterInterface::class),
            DI\get(ControllerFactoryInterface::class),
        ),
    FrontController::class            => DI\create(FrontController::class)
        ->constructor(
            DI\get(CallStackFactory::class),
            DI\get(ErrorHandlerInterface::class),
            DI\get(ResponseSender::class),
        )
        ->method("addMiddleware", DI\get(RequestMeta::class)),
];
