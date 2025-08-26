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
