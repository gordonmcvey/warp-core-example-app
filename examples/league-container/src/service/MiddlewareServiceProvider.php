<?php

declare(strict_types=1);

namespace gordonmcvey\exampleapp\service;

use gordonmcvey\exampleapp\middleware\RequestMeta;
use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Configure any middleware that requires constructor or method arguments.  Middleware that doesn't require arguments
 * don't need to be added here
 */
class MiddlewareServiceProvider extends AbstractServiceProvider
{
    private const array PROVIDED = [
        RequestMeta::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, self::PROVIDED);
    }

    public function register(): void
    {
        $this->container->add(RequestMeta::class)->addArgument("received");
    }
}
