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
