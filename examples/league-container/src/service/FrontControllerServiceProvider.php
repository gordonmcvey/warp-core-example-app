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

class FrontControllerServiceProvider extends AbstractServiceProvider
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
