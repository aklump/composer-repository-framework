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

require_once __DIR__ . '/../inc/_fw.bootstrap.php';

/** @var \Monolog\Logger $logger */

$package_change_manager = new PackageChangeManager(ROOT . '/' . Constants::ROOT_RELATIVE_CACHE_PATH);
$changed_packages = $package_change_manager->getChangedPackages();
if (empty($changed_packages)) {
  echo 'No new package changes.' . PHP_EOL;

  // The server has not received any repository change hooks.  Nothing to do.
  return 1;
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

$result_code = include __DIR__ . '/rebuild.php';
if ($result_code === 0) {
  $logger->info('Repository rebuilt', ['packages' => array_column($satis_content['repositories'], 'url')]);
  // Do this so that next cron run will not repeat work already done.
  $package_change_manager->clearAll();
}
else {
  $logger->error('Failed to rebuild repository', ['packages' => array_column($satis_content['repositories'], 'url')]);
}

return $result_code;

