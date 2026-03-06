<?php
/**
 * @file
 * Can be used to remove a package from the satis.json file.
 */

use AKlump\Packages\SatisManager;

require_once __DIR__ . '/../inc/_fw.bootstrap.php';

if ($argc < 2) {
  echo PHP_EOL;
  echo "Usage: bin/remove <package_url>" . PHP_EOL;
  echo PHP_EOL;
  exit(1);
}

$to_remove = $argv[1];

if (!defined('SATIS_FILE_PATH') || !file_exists(SATIS_FILE_PATH)) {
  echo PHP_EOL;
  echo "❌ Missing SATIS_FILE_PATH." . PHP_EOL;
  echo PHP_EOL;
  exit(1);
}

$satis_manager = new SatisManager(SATIS_FILE_PATH);
$removed_urls = $satis_manager->remove($to_remove);

if (empty($removed_urls)) {
  echo PHP_EOL;
  echo "❓ Package \"$to_remove\" not found in repositories." . PHP_EOL;
  echo PHP_EOL;
  exit(1);
}

foreach ($removed_urls as $url) {
  echo "🗑️ Removing package with URL: $url" . PHP_EOL;
}

echo PHP_EOL;
echo "✅ Package \"$to_remove\" removed from satis.json." . PHP_EOL;
echo "👉 Run `bin/rebuild` to update the repository." . PHP_EOL;
echo PHP_EOL;

exit(0);
