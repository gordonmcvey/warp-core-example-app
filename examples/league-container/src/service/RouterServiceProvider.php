<?php

namespace gordonmcvey\exampleapp\service;

use gordonmcvey\WarpCore\interface\routing\RouterInterface;
use gordonmcvey\WarpCore\interface\routing\RoutingStrategyInterface;
use gordonmcvey\WarpCore\routing\PathNamespaceStrategy;
use gordonmcvey\WarpCore\routing\Router;
use League\Container\ServiceProvider\AbstractServiceProvider;

class RouterServiceProvider extends AbstractServiceProvider
{
    private const array PROVIDED = [
        RoutingStrategyInterface::class,
        RouterInterface::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, self::PROVIDED);
    }

    public function register(): void
    {
        $this->container
            ->add(
                RoutingStrategyInterface::class,
                PathNamespaceStrategy::class,
            )
            ->addArgument(EnvServiceProvider::APP_CONTROLLER_NAMESPACE_ROOT)
        ;

        $this->container
            ->add(
                RouterInterface::class,
                Router::class,
            )
            ->addArgument(RoutingStrategyInterface::class)
        ;
    }
}
