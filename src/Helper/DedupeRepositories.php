<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\Helper;

/**
 * Remove duplicate repositories from array by `url`.
 */
class DedupeRepositories {

  public function __invoke(array &$repositories): void {
    $seen = [];
    $deduped = [];
    foreach ($repositories as $repository) {
      $key = $repository['url'] ?? '';
      if ($key && !in_array($key, $seen)) {
        $deduped[] = $repository;
        $seen[] = $key;
      }
    }
    $repositories = $deduped;
  }
}
