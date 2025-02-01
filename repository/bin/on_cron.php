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


use AKlump\PackagesITLS\Config\GetAPISecret;
use AKlump\PackagesITLS\Config\GetCacheURI;
use AKlump\PackagesITLS\Config\GetRepositoryUrl;
use AKlump\PackagesITLS\DedupeRepositories;
use AKlump\PackagesITLS\ParseRepositories;
use AKlump\PackagesITLS\RepositoryServer;
use AKlump\PackagesITLS\WriteRepositories;

require __DIR__ . '/../vendor/autoload.php';

$server = new RepositoryServer(
  (new GetRepositoryUrl())(),
  (new GetAPISecret())(),
  (new GetCacheURI())(),
);

$changed_dependencies = $server->read();
if (empty($changed_dependencies)) {
  // The server has not received any repository change hooks.  Nothing to do.
  exit(1);
}

// Make sure we have all reporting repositories in our satis.json file.
$path_to_satis = __DIR__ . '/../satis.json';
$repositories = (new ParseRepositories($path_to_satis))();
$repositories = array_merge($repositories, $changed_dependencies);
$repositories = (new DedupeRepositories())($repositories);
(new WriteRepositories($path_to_satis))($repositories);

system(__DIR__ . '/rebuild.sh');
system(__DIR__ . '/publish.sh');

// Do this so that next cron run will not repeat work already done.
$server->flush();
