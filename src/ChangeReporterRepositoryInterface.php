<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages;

interface ChangeReporterRepositoryInterface {

  /**
   * @return \AKlump\Packages\Reporters\ChangeReporterInterface[]
   */
  public function get(): array;
}
