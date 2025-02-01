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

require __DIR__ . '/../vendor/autoload.php';

$file_api = new FileAPIClient(__DIR__ . '/../cli_server.php');

$changed_dependencies = $file_api->getPackages();
if (empty($changed_dependencies)) {
  // The server has not received any repository change hooks.  Nothing to do.
  exit(1);
}

// Make sure we have all reporting repositories in our satis.json file.
$path_to_satis = __DIR__ . '/../data/satis.json';
$repositories = (new ParseRepositories($path_to_satis))();
$repositories = array_merge($repositories, $changed_dependencies);
$repositories = (new DedupeRepositories())($repositories);
(new WriteRepositories($path_to_satis))($repositories);

system(__DIR__ . '/rebuild.sh');

// Do this so that next cron run will not repeat work already done.
$file_api->markPackagesReceived();
