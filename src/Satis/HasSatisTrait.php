<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\Satis;

trait HasSatisTrait {

  private string $satis;

  public function __construct(string $path_to_satis) {
    $this->satis = $path_to_satis;
  }

  public function load(): array {
    return json_decode(file_get_contents($this->satis), TRUE) ?? [];
  }

  public function save(array $data) {
    file_put_contents($this->satis, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  }
}
