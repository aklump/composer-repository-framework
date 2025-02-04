<?php

namespace AKlump\Packages;

use AKlump\Packages\Helper\GetTags;
use AKlump\Packages\Reporters\GithubReporter;

class ChangeReporterRepository implements ChangeReporterRepositoryInterface {

  /**
   * @return \AKlump\Packages\Reporters\ChangeReporterInterface[]
   */
  public function get(): array {
    return [
      new GithubReporter(),
    ];
  }
}
