<?php

declare(strict_types=1);

namespace gordonmcvey\exampleapp\factory;

use gordonmcvey\WarpCore\controller\ControllerFactory;
use gordonmcvey\WarpCore\interface\controller\RequestHandlerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DiControllerFactory extends ControllerFactory
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    /**
     * @inheritDoc
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function make(string $path): RequestHandlerInterface
    {
        $checkedPath = $this->checkControllerExists($path);
        $controller = $this->container->get($checkedPath);
        return $this->checkIsController($controller, $checkedPath);
    }

    public function withArguments(...$arguments): self
    {
        // The container should already be wired with the relevant arguments
        return $this;
    }
}
