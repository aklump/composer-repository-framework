<?php

/**
 * Creates a backup of key configuration files.
 *
 * Usage: php backup.php [--output <filename>]
 *
 * Default output: backup.zip
 *
 * Files included:
 * - app/.env
 * - app/data/.satis.json
 */

require_once __DIR__ . '/../inc/_fw.bootstrap.php';

// Check if 'zip' command is available.
exec('which zip', $zip_output, $zip_return_var);
if ($zip_return_var !== 0) {
  echo "❌ Error: The 'zip' command is not available on this system." . PHP_EOL;
  exit(1);
}

$options = getopt('', ['output:']);
$output_basename = $options['output'] ?? 'composer-repository-backup';

// Ensure the output file has a .zip extension
if (pathinfo($output_basename, PATHINFO_EXTENSION) !== 'zip') {
  $output_basename .= '.zip';
}

$output_file = getcwd() . '/' . $output_basename;

$files_to_backup = [
  ROOT . '/.env',
  ROOT . '/data/.satis.json',
];

// Check if all files exist before proceeding
foreach ($files_to_backup as $file) {
  if (!file_exists($file)) {
    echo "❌ Missing file for backup: " . $file . PHP_EOL;
    exit(1);
  }
}

// Create a temporary directory for the backup process
$temp_dir = sys_get_temp_dir() . '/composer_repository_backup_' . uniqid();
if (!mkdir($temp_dir, 0777, TRUE)) {
  echo "❌ Failed to create temporary directory." . PHP_EOL;
  exit(1);
}

// Copy files to the temporary directory
foreach ($files_to_backup as $file) {
  $dest = $temp_dir . '/' . basename($file);
  if (!copy($file, $dest)) {
    echo "❌ Failed to copy " . $file . " to backup directory." . PHP_EOL;
    // Cleanup
    shell_exec("rm -rf " . escapeshellarg($temp_dir));
    exit(1);
  }
}

// Zip the contents of the temporary directory
// We use 'zip' command via shell for simplicity, as it's common on Unix-like systems.
$zip_command = sprintf(
  'cd %s && zip -j %s .env .satis.json',
  escapeshellarg($temp_dir),
  escapeshellarg($output_file)
);

exec($zip_command, $output, $return_var);

// Cleanup temporary directory
shell_exec("rm -rf " . escapeshellarg($temp_dir));

if ($return_var !== 0) {
  echo "❌ Failed to create backup zip file." . PHP_EOL;
  exit(1);
}

echo "✅ Backup created at: " . $output_file . PHP_EOL;
exit(0);
