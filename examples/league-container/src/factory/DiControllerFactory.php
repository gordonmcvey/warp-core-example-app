<?php

namespace gordonmcvey\exampleapp\factory;

use gordonmcvey\WarpCore\controller\ControllerFactory;
use gordonmcvey\WarpCore\Exceptions\Routing;
use gordonmcvey\WarpCore\interface\controller\RequestHandlerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DiControllerFactory extends ControllerFactory
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function __invoke(string $path): RequestHandlerInterface
    {
        $checkedPath = $this->checkControllerExists($path);
        $controller = $this->container->get($checkedPath);
        return $this->checkIsController($controller, $checkedPath);

    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Routing
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
