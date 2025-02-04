#!/usr/bin/env php
<?php

/**
 * This file is intended to be triggered by a cron job.
 *
 * @package AKlump\PackagesITLS
 */

use AKlump\Packages\Config\Constants;
use AKlump\Packages\Helper\DedupeRepositories;
use AKlump\Packages\PackageChangeManager;
use AKlump\Packages\SatisManager;

require __DIR__ . '/../inc/_fw.bootstrap.php';

/** @var \Monolog\Logger $logger */

$package_change_manager = new PackageChangeManager(ROOT . '/' . Constants::ROOT_RELATIVE_CACHE_PATH);
$changed_packages = $package_change_manager->getChangedPackages();
if (empty($changed_packages)) {
  echo 'No new package changes.' . PHP_EOL;
  // The server has not received any repository change hooks.  Nothing to do.
  exit(1);
}

$newly_changed_repositories = [];
foreach ($changed_packages as $changed_package) {
  $newly_changed_repositories = array_merge($newly_changed_repositories, [$changed_package['repository']]);
}

// Make sure we have all reporting repositories in our satis.json file.
$satis_manager = new SatisManager(SATIS_FILE_PATH);
$satis_content = $satis_manager->load();
$satis_content['repositories'] = array_merge($satis_content['repositories'], $newly_changed_repositories);
(new DedupeRepositories())($satis_content['repositories']);
$satis_manager->save($satis_content);

$logger->info('Repository rebuilt', ['packages' => array_column($satis_content['repositories'], 'url')]);

// TODO Can't we just require this?
system(ROOT . '/bin/rebuild.php', $result_code);
if ($result_code === 0) {
  // Do this so that next cron run will not repeat work already done.
  $package_change_manager->clearAll();
}

