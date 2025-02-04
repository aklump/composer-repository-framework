<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages;


use AKlump\Packages\Helper\DedupePackages;

class PackageChangeManager {

  private string $cacheDir;

  public function __construct(string $cache_dir) {
    $this->cacheDir = $cache_dir;
  }

  public function reportChanges(array $packages): bool {
    $storage_file = $this->getStorageFilePath();
    if (file_exists($storage_file)) {
      $packages = $this->mergePackages($packages);
    }

    return (bool) file_put_contents($storage_file, json_encode($packages, JSON_UNESCAPED_SLASHES));
  }

  public function getChangedPackages() {
    $storage_file = $this->getStorageFilePath();
    if (!file_exists($storage_file)) {
      return [];
    }

    return json_decode(file_get_contents($storage_file), TRUE) ?? [];
  }

  public function clearAll(): void {
    if (file_exists($this->getStorageFilePath())) {
      unlink($this->getStorageFilePath());
    }
  }

  private function getStorageFilePath(): string {
    if (!file_exists($this->cacheDir)) {
      mkdir($this->cacheDir, 0755, TRUE);
    }

    return $this->cacheDir . '/changed.json';
  }

  private function mergePackages(array $packages): array {
    $storage_file = $this->getStorageFilePath();
    $queued = json_decode(file_get_contents($storage_file), TRUE);
    $packages = array_merge($packages, $queued);
    (new DedupePackages())($packages);

    return $packages;
  }

}
