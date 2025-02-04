<?php
// SPDX-License-Identifier: BSD-3-Clause

/**
 * This file initializes and loads environment variables using the Dotenv package.
 * Ensure the `.env` file is properly configured in the root directory.
 */

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

if (!defined('ROOT')) {
  if (!empty($_ENV['ROOT'])) {
    define('ROOT', $_ENV['ROOT']);
  }
  else {
    define('ROOT', realpath(__DIR__ . '/..'));
  }
}

const SATIS_CANONICAL_PATH = ROOT . '/satis.json';
const SATIS_FILE_PATH = ROOT . '/data/.satis.json';

$_composer_autoload_path = ROOT . '/vendor/autoload.php';
if (!file_exists($_composer_autoload_path)) {
  throw new RuntimeException(sprintf('Missing dependencies; have you tried %s/bin/install.sh?', ROOT));
}
$class_loader = require_once $_composer_autoload_path;

$dotenv = Dotenv::createImmutable(ROOT);
$dotenv->load();

$logger = new Logger('cron');
$logger->pushHandler(new StreamHandler(ROOT . '/data/packages.log', Logger::DEBUG));

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
