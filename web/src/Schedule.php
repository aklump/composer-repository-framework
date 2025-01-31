<?php

namespace InTheLoftStudios\Packages;

class Schedule {

  private string $cacheDir;

  public function __construct(string $cache_dir) {
    $this->cacheDir = $cache_dir;
  }

  public function __invoke(array $repositories): bool {
    $queue_path = $this->getQueuePath();
    if (file_exists($queue_path)) {
      $repositories = $this->mergeRepositories($repositories);
    }

    return (bool) file_put_contents($queue_path, json_encode($repositories, JSON_UNESCAPED_SLASHES));
  }

  private function getQueuePath(): string {
    if (!file_exists($this->cacheDir)) {
      mkdir($this->cacheDir, 0755, TRUE);
    }

    return $this->cacheDir . '/publish.json';
  }

  private function mergeRepositories(array $repositories): array {
    $queue_path = $this->getQueuePath();
    $queued = json_decode(file_get_contents($queue_path), TRUE);
    $repositories = array_merge($repositories, $queued);

    return array_unique($repositories);
  }
}
