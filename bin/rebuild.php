<?php

/**
 * This file is intended to be triggered by a cron job.
 *
 * @package AKlump\PackagesITLS
 */

use AKlump\Packages\SatisManager;

require __DIR__ . '/../inc/_fw.bootstrap.php';

/** @var \Monolog\Logger $logger */

if (!defined('SATIS_FILE_PATH')
  || !defined('SATIS_CANONICAL_PATH')
  || (!file_exists(SATIS_FILE_PATH) && !copy(SATIS_CANONICAL_PATH, SATIS_FILE_PATH))
) {
  echo PHP_EOL;
  echo "❌ Missing SATIS_FILE_PATH." . PHP_EOL;
  echo PHP_EOL;
  exit(1);
}

$canonical_data = json_decode(file_get_contents(SATIS_CANONICAL_PATH), TRUE);
$satis_manager = new SatisManager(SATIS_FILE_PATH);
$satis_data = $satis_manager->load();

// Overwrite with canonical data as appropriate.
$satis_data['name'] = $canonical_data['name'];
$satis_data['homepage'] = $canonical_data['homepage'];
$satis_data['require-all'] = TRUE;
$satis_data['repositories'] = $satis_data['repositories'] ?? [];
$satis_manager->save($satis_data);

$command = sprintf('%s/vendor/bin/satis build %s %s/web', ROOT, SATIS_FILE_PATH, ROOT);
system($command, $result_code);
if (0 !== $result_code) {
  system(sprintf('export ROOT=%s;%s/bin/check_config.sh', ROOT, ROOT));
  echo PHP_EOL;
  echo "❌ Failed to build repository." . PHP_EOL;
  echo PHP_EOL;
  exit(1);
}
echo PHP_EOL;
echo '📦 Package repository rebuilt.' . PHP_EOL;
echo PHP_EOL;
exit(0);
