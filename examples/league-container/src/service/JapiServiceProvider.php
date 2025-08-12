<?php

namespace gordonmcvey\exampleapp\service;

use gordonmcvey\exampleapp\factory\DiControllerFactory;
use gordonmcvey\exampleapp\middleware\RequestMeta;
use gordonmcvey\httpsupport\interface\response\ResponseSenderInterface;
use gordonmcvey\WarpCore\Bootstrap;
use gordonmcvey\WarpCore\FrontController;
use gordonmcvey\WarpCore\interface\controller\ControllerFactoryInterface;
use gordonmcvey\WarpCore\interface\error\ErrorHandlerInterface;
use gordonmcvey\WarpCore\interface\routing\RouterInterface;
use gordonmcvey\WarpCore\middleware\CallStackFactory;
use League\Container\ServiceProvider\AbstractServiceProvider;

class JapiServiceProvider extends AbstractServiceProvider
{
    private const array PROVIDED = [
        Bootstrap::class,
        ControllerFactoryInterface::class,
        FrontController::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, self::PROVIDED);
    }

    public function register(): void
    {
        $this->container->add(ControllerFactoryInterface::class, DiControllerFactory::class)->addArgument($this->container);
        $this->container->add(Bootstrap::class)->addArguments([RouterInterface::class, ControllerFactoryInterface::class]);
        $this->container->add(FrontController::class)
            ->addArguments([CallStackFactory::class, ErrorHandlerInterface::class, ResponseSenderInterface::class])
            ->addMethodCall("addMiddleware", [RequestMeta::class])
        ;
    }
}
