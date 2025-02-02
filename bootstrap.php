<?php
// SPDX-License-Identifier: BSD-3-Clause

/**
 * This file initializes and loads environment variables using the Dotenv package.
 * Ensure the `.env` file is properly configured in the root directory.
 */

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// https://getcomposer.org/doc/articles/vendor-binaries.md#finding-the-composer-autoloader-from-a-binary
if (isset($GLOBALS['_composer_autoload_path'])) {
  // As of Composer 2.2...
  $_composer_autoload_path = $GLOBALS['_composer_autoload_path'];
}
else {
  // < Composer 2.2
  foreach ([
             __DIR__ . '/../../autoload.php',
             __DIR__ . '/../vendor/autoload.php',
             __DIR__ . '/vendor/autoload.php',
           ] as $_composer_autoload_path) {
    if (file_exists($_composer_autoload_path)) {
      break;
    }
  }
}

if (!file_exists($_composer_autoload_path)) {
  throw new \RuntimeException(sprintf('Missing dependencies; have you tried ./bin/install.sh?'));
}

$class_loader = require_once $_composer_autoload_path;

if (!defined('ROOT')) {
  define('ROOT', __DIR__);
}

define('SATIS_FILE_PATH', realpath(ROOT . '/data/.satis.json'));

$dotenv = Dotenv::createImmutable(ROOT);
$dotenv->load();

$logger = new Logger('cron');
$logger->pushHandler(new StreamHandler(__DIR__ . '/data/packages.log', Logger::DEBUG));

if (!function_exists('getallheaders')) {
  function getallheaders() {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
      if ($name != 'HTTP_MOD_REWRITE' && (substr($name, 0, 5) == 'HTTP_' || $name == 'CONTENT_LENGTH' || $name == 'CONTENT_TYPE')) {
        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', str_replace('HTTP_', '', $name)))));
        if ($name == 'Content-Type') {
          $name = 'Content-type';
        }
        $headers[$name] = $value;
      }
    }

    return $headers;
  }
}
