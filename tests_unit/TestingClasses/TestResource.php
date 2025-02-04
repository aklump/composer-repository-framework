<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit\TestingClasses;

use AKlump\Packages\API\Resource\ResourceInterface;
use AKlump\Packages\ChangeReporterRepositoryInterface;
use AKlump\AnnotatedResponse\AnnotatedResponseInterface;
use Monolog\Logger;

class TestResource implements ResourceInterface {

  public function setChangeReporterRepository(ChangeReporterRepositoryInterface $change_reporter_repository): self {
    return $this;
  }

  public function get(): AnnotatedResponseInterface {
    // TODO: Implement get() method.
  }

  public function post(string $content): AnnotatedResponseInterface {
    // TODO: Implement post() method.
  }

  public function delete(): AnnotatedResponseInterface {
    // TODO: Implement delete() method.
  }
}
