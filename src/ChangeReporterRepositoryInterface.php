<?php

namespace AKlump\Packages;

interface ChangeReporterRepositoryInterface {

  /**
   * @return \AKlump\Packages\Reporters\ChangeReporterInterface[]
   */
  public function get(): array;
}
