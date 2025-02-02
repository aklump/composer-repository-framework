<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\Helper;

class DedupePackages {

  public function __invoke(array $repositories): array {
    $seen = [];
    $deduped = [];
    foreach ($repositories as $repository) {
      $key = $repository['repositories'][0]['url'] ?? '';
      if ($key && !in_array($key, $seen)) {
        $deduped[] = $repository;
        $seen[] = $key;
      }
    }

    return $deduped;
  }
}
