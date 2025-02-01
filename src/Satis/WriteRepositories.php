<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\Satis;

class WriteRepositories {

  use HasSatisTrait;

  /**
   * @param array $repositories This value will replace existing, so be sure to load/merge if so desired.
   *
   * @return void
   *
   * @see \AKlump\Packages\Helper\DedupeRepositories
   */
  public function __invoke(array $repositories) {
    $data = $this->load();
    $data['repositories'] = $repositories;
    $this->save($data);
  }
}
