<?php
// SPDX-License-Identifier: BSD-3-Clause

/**
 * This file initializes and loads environment variables using the Dotenv package.
 * Ensure the `.env` file is properly configured in the root directory.
 */

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$logger = new Logger('cron');
$logger->pushHandler(new StreamHandler(__DIR__ . '/data/packages.log', Logger::DEBUG));
