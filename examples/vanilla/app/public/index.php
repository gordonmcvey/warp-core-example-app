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

$received = new DateTimeImmutable();

use Dotenv\Dotenv;
use gordonmcvey\exampleapp\controller\Health\Ping;
use gordonmcvey\exampleapp\middleware\ProcessedTime;
use gordonmcvey\exampleapp\middleware\RequestMeta;
use gordonmcvey\httpsupport\enum\factory\StatusCodeFactory;
use gordonmcvey\httpsupport\interface\request\RequestInterface;
use gordonmcvey\httpsupport\request\payload\JsonPayloadHandler;
use gordonmcvey\httpsupport\request\Request;
use gordonmcvey\httpsupport\response\sender\ResponseSender;
use gordonmcvey\WarpCore\controller\ControllerFactory;
use gordonmcvey\WarpCore\error\JsonErrorHandler;
use gordonmcvey\WarpCore\ErrorToException;
use gordonmcvey\WarpCore\FrontController;
use gordonmcvey\WarpCore\interface\controller\RequestHandlerInterface;
use gordonmcvey\WarpCore\middleware\CallStackFactory;
use gordonmcvey\WarpCore\routing\PathNamespaceStrategy;
use gordonmcvey\WarpCore\routing\Router;
use gordonmcvey\WarpCore\ShutdownHandler;

require_once __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$dotenv->ifPresent("ERROR_REPORTING")->isInteger();
error_reporting((int) $_ENV["ERROR_REPORTING"]);

$dotenv->ifPresent("DISPLAY_ERRORS");
ini_set("display_errors", (string) $_ENV["DISPLAY_ERRORS"]);

$dotenv->ifPresent("DISPLAY_STARTUP_ERRORS")->isBoolean();
ini_set("display_startup_errors", (bool) $_ENV["DISPLAY_STARTUP_ERRORS"]);

set_error_handler(new errorToException(), E_ERROR ^ E_USER_ERROR ^ E_COMPILE_ERROR);

$dotenv->ifPresent("PRETTY_PRINT_JSON")->isBoolean();
$dotenv->ifPresent("DETAILED_ERROR_OUTPUT")->isBoolean();
$errorHandler = new JsonErrorHandler(
    statusCodeFactory: new StatusCodeFactory(),
    jsonFlags: ((bool) $_ENV["PRETTY_PRINT_JSON"]) ? JSON_PRETTY_PRINT : 0,
    exposeDetails: (bool) $_ENV["DETAILED_ERROR_OUTPUT"],
);

$sender = new ResponseSender();
register_shutdown_function(new ShutdownHandler($sender, $errorHandler));

$dotenv->required("APP_CONTROLLER_NAMESPACE_ROOT");

(new FrontController(new CallStackFactory(), $errorHandler, $sender))
    ->addMiddleware(new RequestMeta($received))
    ->bootstrap(
        function (RequestInterface $request): RequestHandlerInterface {
            $router = new Router(new PathNamespaceStrategy($_ENV["APP_CONTROLLER_NAMESPACE_ROOT"]));
            $controller = (new ControllerFactory())->make($router->route($request));
            $controller instanceof Ping && $controller->addMiddleware(new ProcessedTime());

            return $controller;
        },
        Request::fromSuperGlobals(new JsonPayloadHandler()),
    )
;
