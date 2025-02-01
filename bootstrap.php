<?php
// SPDX-License-Identifier: BSD-3-Clause

/**
 * This file initializes and loads environment variables using the Dotenv package.
 * Ensure the `.env` file is properly configured in the root directory.
 */

use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
