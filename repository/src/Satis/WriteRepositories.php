<?php

namespace AKlump\PackagesITLS\Satis;

class WriteRepositories {

  use HasSatisTrait;

  /**
   * @param array $repositories This value will replace existing, so be sure to load/merge if so desired.
   *
   * @return void
   *
   * @see \AKlump\PackagesITLS\Helper\DedupeRepositories
   */
  public function __invoke(array $repositories) {
    $data = $this->load();
    $data['repositories'] = $repositories;
    $this->save($data);
  }
}
