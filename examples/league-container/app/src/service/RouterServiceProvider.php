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

use gordonmcvey\exampleapp\controller\Health\EchoPayload;
use gordonmcvey\exampleapp\controller\Health\Ping;
use gordonmcvey\httpsupport\enum\Verbs;
use gordonmcvey\WarpCore\interface\routing\RouterInterface;
use gordonmcvey\WarpCore\routing\RequestPathValidator;
use gordonmcvey\WarpCore\routing\Router;
use gordonmcvey\WarpCore\routing\StaticStrategy;
use League\Container\ServiceProvider\AbstractServiceProvider;

class RouterServiceProvider extends AbstractServiceProvider
{
    private const array PROVIDED = [
        RouterInterface::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, self::PROVIDED);
    }

    public function register(): void
    {
        $this->container
            ->add("PingRoute", StaticStrategy::class)
            ->addArgument(["/health/ping" => Ping::class])
            ->addArgument(Verbs::GET)
            ->addArgument(Verbs::HEAD)
        ;

        $this->container
            ->add("EchoPayloadRoute", StaticStrategy::class)
            ->addArgument(["/health/echo-payload" => EchoPayload::class])
            ->addArgument(Verbs::PUT)
            ->addArgument(Verbs::POST)
        ;

        $this->container
            ->add(
                RouterInterface::class,
                Router::class,
            )
            ->addArgument(RequestPathValidator::class)
            ->addArgument($this->container->get("PingRoute"))
            ->addArgument($this->container->get("EchoPayloadRoute"))
        ;
    }
}
