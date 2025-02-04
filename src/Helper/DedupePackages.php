<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\Helper;

/**
 * Remove duplicate packages from array by `repository.url`.
 */
class DedupePackages {

  public function __invoke(array &$packages): void {
    $seen = [];
    $deduped = [];
    foreach ($packages as $package) {
      $key = $package['repository']['url'] ?? '';
      if ($key && !in_array($key, $seen)) {
        $deduped[] = $package;
        $seen[] = $key;
      }
    }
    $packages = $deduped;
  }
}
