<?php

namespace AKlump\PackagesITLS\Helper;

class DedupeRepositories {

  public function __invoke(array $repositories): array {
    $seen = [];
    $deduped = [];
    foreach ($repositories as $repository) {
      $key = json_encode($repository);
      if (!in_array($key, $seen)) {
        $deduped[] = $repository;
        $seen[] = $key;
      }
    }

    return $deduped;
  }
}
