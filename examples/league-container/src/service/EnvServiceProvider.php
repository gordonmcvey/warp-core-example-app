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

use Dotenv\Dotenv;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

class EnvServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public const string APP_CONTROLLER_NAMESPACE_ROOT = "APP_CONTROLLER_NAMESPACE_ROOT";
    public const string DETAILED_ERROR_OUTPUT         = "DETAILED_ERROR_OUTPUT";
    public const string DISPLAY_ERRORS                = "DISPLAY_ERRORS";
    public const string DISPLAY_STARTUP_ERRORS        = "DISPLAY_STARTUP_ERRORS";
    public const string ERROR_REPORTING               = "ERROR_REPORTING";
    public const string PRETTY_PRINT_JSON             = "PRETTY_PRINT_JSON";

    private const array PROVIDED = [
        self::APP_CONTROLLER_NAMESPACE_ROOT,
        self::DETAILED_ERROR_OUTPUT,
        self::DISPLAY_ERRORS,
        self::DISPLAY_STARTUP_ERRORS,
        self::ERROR_REPORTING,
        self::PRETTY_PRINT_JSON,
    ];

    public function __construct(readonly private string $envDir)
    {
    }

    public function boot(): void
    {
        $dotenv = Dotenv::createImmutable($this->envDir);
        $dotenv->load();

        $dotenv->required(self::APP_CONTROLLER_NAMESPACE_ROOT);
        $dotenv->ifPresent(self::ERROR_REPORTING)->isInteger();
        $dotenv->ifPresent(self::DISPLAY_ERRORS);
        $dotenv->ifPresent(self::DISPLAY_STARTUP_ERRORS)->isBoolean();
        $dotenv->ifPresent(self::PRETTY_PRINT_JSON)->isBoolean();
        $dotenv->ifPresent(self::DETAILED_ERROR_OUTPUT)->isBoolean();
    }

    public function provides(string $id): bool
    {
        return in_array($id, self::PROVIDED);
    }

    public function register(): void
    {
        $this->container->add(self::APP_CONTROLLER_NAMESPACE_ROOT, $_ENV[self::APP_CONTROLLER_NAMESPACE_ROOT]);
        $this->container->add(self::DETAILED_ERROR_OUTPUT, (bool) $_ENV[self::DETAILED_ERROR_OUTPUT]);
        $this->container->add(self::DISPLAY_ERRORS, (string) $_ENV[self::DISPLAY_ERRORS]);
        $this->container->add(self::DISPLAY_STARTUP_ERRORS, (bool) $_ENV[self::DISPLAY_STARTUP_ERRORS]);
        $this->container->add(self::ERROR_REPORTING, (int) $_ENV[self::ERROR_REPORTING]);
        $this->container->add(
            self::PRETTY_PRINT_JSON,
            ((bool) $_ENV[self::PRETTY_PRINT_JSON]) ? JSON_PRETTY_PRINT : 0,
        );
    }
}
