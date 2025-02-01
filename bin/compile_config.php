#!/usr/bin/env php
<?php

/**
 * @flag --force Use this to overwrite existing files.
 */

$master_config = json_decode(file_get_contents(__DIR__ . '/../config.json'), TRUE);

$force = FALSE;
if (in_array('--force', $argv)) {
  $force = TRUE;
}

$config_file = __DIR__ . '/../repository_manager/.env';
if ($force || !file_exists($config_file)) {
  $config = <<<EOD
  CACHE_URI={$master_config['CACHE_URI']}
  API_SECRET={$master_config['API_SECRET']}
  EOD;
  file_put_contents($config_file, $config);
  echo "✅ $config_file created." . PHP_EOL;
}

$config_file = __DIR__ . '/../repository/.env';
if ($force || !file_exists($config_file)) {
  $config = <<<EOD
  CACHE_URI={$master_config['CACHE_URI']}
  REPOSITORY_URL={$master_config['REPOSITORY_URL']}
  SSH_USER={$master_config['SSH_USER']}
  SSH_HOST={$master_config['SSH_HOST']}
  SSH_SERVER_PATH_TO_WEBROOT={$master_config['SSH_SERVER_PATH_TO_WEBROOT']}
  EOD;
  file_put_contents($config_file, $config);
  echo "✅ $config_file created." . PHP_EOL;
}

$config_file = __DIR__ . '/../repository/satis.json';
if ($force || !file_exists($config_file)) {
  $data = $master_config['satis.json'] ?? [];
  $data['homepage'] = $master_config['REPOSITORY_URL'];
  $data['require-all'] = TRUE;
  $data['repositories'] = [];
  file_put_contents($config_file, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  echo "✅ $config_file created." . PHP_EOL;
}
