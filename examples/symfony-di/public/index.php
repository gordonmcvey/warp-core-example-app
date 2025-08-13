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

use Dotenv\Dotenv;
use gordonmcvey\httpsupport\request\Request;
use gordonmcvey\WarpCore\ErrorToException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$dotenv->ifPresent("ERROR_REPORTING")->isInteger();
error_reporting(((int) $_ENV["ERROR_REPORTING"]) ?? 0);

$dotenv->ifPresent("DISPLAY_ERRORS");
ini_set("display_errors", ((string) $_ENV["DISPLAY_ERRORS"]) ?? "");

$dotenv->ifPresent("DISPLAY_STARTUP_ERRORS")->isBoolean();
ini_set("display_startup_errors", (bool) $_ENV["DISPLAY_STARTUP_ERRORS"]);

set_error_handler(new ErrorToException(), E_ERROR ^ E_USER_ERROR ^ E_COMPILE_ERROR);

$container = new ContainerBuilder();

$fileLoader = new XmlFileLoader($container, new FileLocator(__DIR__ . "/../config/"));
$fileLoader->load("errorhandler.xml");

register_shutdown_function($container->get("ShutdownHandler"));

$container->set("received", $received);
$container->set("containerObject", $container);

$fileLoader->load("middleware.xml");
$fileLoader->load("controllers.xml");
$fileLoader->load("routing.xml");
$fileLoader->load("frontcontroller.xml");

$container->get("FrontController")
    ->bootstrap(
        $container->get("Bootstrap"),
        Request::fromSuperGlobals($container->get("PayloadHandlerInterface")),
    )
;
