<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages;


use AKlump\Packages\Helper\DedupePackages;

class Schedule {

  private string $cacheDir;

  public function __construct(string $cache_dir) {
    $this->cacheDir = $cache_dir;
  }

  public function add(array $packages): bool {
    $queue_path = $this->getQueuePath();
    if (file_exists($queue_path)) {
      $packages = $this->mergePackages($packages);
    }

    return (bool) file_put_contents($queue_path, json_encode($packages, JSON_UNESCAPED_SLASHES));
  }

  public function getQueuePath(): string {
    if (!file_exists($this->cacheDir)) {
      mkdir($this->cacheDir, 0755, TRUE);
    }

    return $this->cacheDir . '/changed.json';
  }

  private function mergePackages(array $packages): array {
    $queue_path = $this->getQueuePath();
    $queued = json_decode(file_get_contents($queue_path), TRUE);
    $packages = array_merge($packages, $queued);

    return (new DedupePackages())($packages);
  }
}
