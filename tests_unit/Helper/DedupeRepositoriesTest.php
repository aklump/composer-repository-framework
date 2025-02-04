<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit;

use AKlump\Packages\Helper\DedupePackages;
use AKlump\Packages\Helper\DedupeRepositories;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\Packages\Helper\DedupeRepositories
 */
class DedupeRepositoriesTest extends TestCase {

  public static function dataFortestInvokeProvider(): array {
    $tests = [];
    $tests[] = [
      [],
      [],
    ];
    $tests[] = [
      [
        ['url' => 'https://github.com/aklump/composer-repository-framework'],
        ['url' => 'https://github.com/aklump/composer-repository-framework'],
      ],
      [
        ['url' => 'https://github.com/aklump/composer-repository-framework'],
      ],
    ];

    return $tests;
  }

  /**
   * @dataProvider dataFortestInvokeProvider
   */
  public function testInvoke($packages, $expected) {
    (new DedupeRepositories())($packages);
    $this->assertEquals($expected, $packages);
  }
}
