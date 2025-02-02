#!/usr/bin/env php
<?php

/**
 * This file is intended to be triggered by a cron job.
 *
 * It processes repository changes detected via GitHub webhooks, updates the
 * `satis.json` file, and rebuilds the Composer repository.
 *
 * Workflow:
 * - Reads repository changes from the remote server queue.
 * - Updates and deduplicates entries in `satis.json`.
 * - Rebuilds and publishes the updated packages.
 * - Clears the server queue to prevent reprocessing.
 *
 * @package AKlump\PackagesITLS
 */

use AKlump\Packages\Helper\DedupeRepositories;
use AKlump\Packages\Satis\ParseRepositories;
use AKlump\Packages\Satis\WriteRepositories;
use AKlump\Packages\API\FileAPIClient;

require __DIR__ . '/../bootstrap.php';

/** @var \Monolog\Logger $logger */

const SATIS_FILE_PATH = __DIR__ . '/../data/satis.json';

$file_api = new FileAPIClient(__DIR__ . '/../cli_server.php');

$changed_packages = $file_api->getPackages();
if (empty($changed_packages)) {
  // The server has not received any repository change hooks.  Nothing to do.
  exit(1);
}

$changed_dependencies = [];
foreach ($changed_packages as $changed_package) {
  $changed_dependencies = array_merge($changed_dependencies, $changed_package['repositories']);
}

// Make sure we have all reporting repositories in our satis.json file.
$repositories = (new ParseRepositories(SATIS_FILE_PATH))();
$repositories = array_merge($repositories, $changed_dependencies);
$repositories = (new DedupeRepositories())($repositories);
(new WriteRepositories(SATIS_FILE_PATH))($repositories);

$logger->info('Repository rebuilt', ['packages' => array_column($repositories, 'url')]);

system(__DIR__ . '/rebuild.sh', $result_code);
if ($result_code === 0) {
  // Do this so that next cron run will not repeat work already done.
  $file_api->deletePackages();
}

