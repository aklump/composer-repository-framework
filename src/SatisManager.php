<?php

namespace AKlump\Packages;

class SatisManager {

  private string $satis;

  public function __construct(string $path_to_satis) {
    $this->satis = $path_to_satis;
  }

  public function load(): array {
    $data = [];
    if (file_exists($this->satis)) {
      $data = json_decode(file_get_contents($this->satis), TRUE) ?? [];
    }
    $data += ['repositories' => []];

    return $data;
  }

  public function save(array $data) {
    file_put_contents($this->satis, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  }
}
