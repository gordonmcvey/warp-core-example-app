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

$received = new DateTimeImmutable();

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use gordonmcvey\httpsupport\interface\request\RequestInterface;
use gordonmcvey\WarpCore\Bootstrap;
use gordonmcvey\WarpCore\ErrorToException;
use gordonmcvey\WarpCore\FrontController;
use gordonmcvey\WarpCore\ShutdownHandler;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$dotenv->required("APP_CONTROLLER_NAMESPACE_ROOT");
$dotenv->ifPresent("DETAILED_ERROR_OUTPUT")->isBoolean();
$dotenv->required("DI_COMPILE_DIR");
$dotenv->ifPresent("DISPLAY_ERRORS");
$dotenv->ifPresent("DISPLAY_STARTUP_ERRORS")->isBoolean();
$dotenv->ifPresent("ERROR_REPORTING")->isInteger();
$dotenv->ifPresent("PRETTY_PRINT_JSON")->isBoolean();

error_reporting(((int) $_ENV["ERROR_REPORTING"]) ?? 0);
ini_set("display_errors", ((string) $_ENV["DISPLAY_ERRORS"]) ?? "");
ini_set("display_startup_errors", (bool) $_ENV["DISPLAY_STARTUP_ERRORS"]);
set_error_handler(new errorToException(), E_ERROR ^ E_USER_ERROR ^ E_COMPILE_ERROR);

$builder = new ContainerBuilder();
if (!empty($_ENV["DI_COMPILE_DIR"])) {
    $builder->enableCompilation($_ENV["DI_COMPILE_DIR"]);
}
$container = $builder
    ->addDefinitions(__DIR__ . '/../config/di/containerconfig.php')
    ->build()
;

$container->set("received", $received);

register_shutdown_function($container->get(ShutdownHandler::class));

$container->get(FrontController::class)->bootstrap(
    $container->get(Bootstrap::class),
    $container->get(RequestInterface::class),
);
