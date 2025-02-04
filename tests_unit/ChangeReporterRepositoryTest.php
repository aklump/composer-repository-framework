<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit;

use AKlump\Packages\ChangeReporterRepository;
use AKlump\Packages\Reporters\ChangeReporterInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\Packages\ChangeReporterRepository
 * @uses \AKlump\Packages\Reporters\GithubReporter
 */
class ChangeReporterRepositoryTest extends TestCase {

  public function testGet() {
    $reporters = (new ChangeReporterRepository())->get();
    $this->assertNotEmpty($reporters);
    $this->assertInstanceOf(ChangeReporterInterface::class, $reporters[0]);
  }
}
