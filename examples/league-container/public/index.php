<?php

declare(strict_types=1);

$received = new DateTimeImmutable();

use gordonmcvey\exampleapp\service\ControllerServiceProvider;
use gordonmcvey\exampleapp\service\EnvServiceProvider;
use gordonmcvey\exampleapp\service\ErrorHandlerServiceProvider;
use gordonmcvey\exampleapp\service\FrontControllerServiceProvider;
use gordonmcvey\exampleapp\service\MiddlewareServiceProvider;
use gordonmcvey\exampleapp\service\RouterServiceProvider;
use gordonmcvey\httpsupport\interface\response\ResponseSenderInterface;
use gordonmcvey\httpsupport\request\payload\JsonPayloadHandler;
use gordonmcvey\httpsupport\request\Request;
use gordonmcvey\httpsupport\response\sender\ResponseSender;
use gordonmcvey\WarpCore\Bootstrap;
use gordonmcvey\WarpCore\ErrorToException;
use gordonmcvey\WarpCore\FrontController;
use gordonmcvey\WarpCore\ShutdownHandler;
use League\Container\Container;
use League\Container\ReflectionContainer;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container();

$container
    ->delegate(new ReflectionContainer(true))
    ->defaultToShared()
;

$container->add(ResponseSenderInterface::class, ResponseSender::class);
$container->addServiceProvider(new EnvServiceProvider(__DIR__ . "/../"));

error_reporting($container->get(EnvServiceProvider::ERROR_REPORTING));
ini_set("display_errors", ($container->get(EnvServiceProvider::DISPLAY_ERRORS)));
ini_set("display_startup_errors", (bool) $container->get(EnvServiceProvider::DISPLAY_STARTUP_ERRORS));
set_error_handler(new errorToException(), E_ERROR ^ E_USER_ERROR ^ E_COMPILE_ERROR);

$container->addServiceProvider(new ErrorHandlerServiceProvider());
register_shutdown_function($container->get(ShutdownHandler::class));

$container->add("received", $received);

$container
    ->addServiceProvider(new MiddlewareServiceProvider())
    ->addServiceProvider(new ControllerServiceProvider())
    ->addServiceProvider(new RouterServiceProvider())
    ->addServiceProvider(new FrontControllerServiceProvider())
    ->get(FrontController::class)
    ->bootstrap(
        $container->get(Bootstrap::class),
        Request::fromSuperGlobals($container->get(JsonPayloadHandler::class)),
    )
;
