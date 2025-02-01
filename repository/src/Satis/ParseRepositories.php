<?php

namespace AKlump\PackagesITLS\Satis;

class ParseRepositories {

  use HasSatisTrait;

  public function __invoke(): array {
    if (!file_exists($this->satis)) {
      return [];
    }

    return $this->load()['repositories'] ?? [];
  }
}
