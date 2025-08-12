<?php

declare(strict_types=1);

namespace gordonmcvey\exampleapp\service;

use gordonmcvey\exampleapp\controller\Health\Ping;
use gordonmcvey\exampleapp\middleware\ProcessedTime;
use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Configures any controllers that require constructor or method arguments.  Controllers that don't require arguments
 * don't need to be added here
 */
class ControllerServiceProvider extends AbstractServiceProvider
{
    private const array PROVIDED = [
        Ping::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, self::PROVIDED);
    }

    public function register(): void
    {
        // The ping controller specifically needs ProcessedTime middleware to provide its full output
        $this->container->add(Ping::class)->addMethodCall("addMiddleware", [ProcessedTime::class]);
    }
}