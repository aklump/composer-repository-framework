<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit;

use AKlump\Packages\Helper\DedupePackages;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\Packages\Helper\DedupePackages
 */
class DedupePackagesTest extends TestCase {

  public static function dataFortestInvokeProvider(): array {
    $tests = [];
    $tests[] = [
      [],
      [],
    ];
    $tests[] = [
      [
        [
          'repository' => ['url' => 'https://github.com/aklump/composer-repository-framework'],
        ],
        [
          'repository' => ['url' => 'https://github.com/aklump/composer-repository-framework'],
        ],
      ],
      [
        [
          'repository' => ['url' => 'https://github.com/aklump/composer-repository-framework'],
        ],
      ],
    ];

    return $tests;
  }

  /**
   * @dataProvider dataFortestInvokeProvider
   */
  public function testInvoke($packages, $expected) {
    (new DedupePackages())($packages);
    $this->assertEquals($expected, $packages);
  }
}
