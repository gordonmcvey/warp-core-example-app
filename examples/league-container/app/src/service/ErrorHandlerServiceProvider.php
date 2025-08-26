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

use gordonmcvey\httpsupport\enum\factory\StatusCodeFactory;
use gordonmcvey\WarpCore\error\JsonErrorHandler;
use gordonmcvey\WarpCore\interface\error\ErrorHandlerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;

class ErrorHandlerServiceProvider extends AbstractServiceProvider
{
    private const array PROVIDED = [
        ErrorHandlerInterface::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, self::PROVIDED);
    }


    public function register(): void
    {
        $this->container->add(ErrorHandlerInterface::class, JsonErrorHandler::class)
            ->addArguments([
                StatusCodeFactory::class,
                EnvServiceProvider::PRETTY_PRINT_JSON,
                EnvServiceProvider::DETAILED_ERROR_OUTPUT,
            ]);
    }
}
