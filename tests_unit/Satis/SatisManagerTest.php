<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit\Satis;

use AKlump\ComposerRepositoryFramework\Tests\Unit\TestingTraits\TestWithFilesTrait;
use AKlump\Packages\SatisManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\Packages\SatisManager
 */
class SatisManagerTest extends TestCase {

  use TestWithFilesTrait;

  function dataFortestLoadProvider(): array {
    $tests = [];
    $tests[] = [
      '[{"name":"aklump/json-schema-merge","version":"","repositories":[{"type":"github","url":"https://github.com/aklump/json-schema-merge"}]}]',
      [
        [
          'name' => 'aklump/json-schema-merge',
          'version' => '',
          'repositories' =>
            [
              [
                'type' => 'github',
                'url' => 'https://github.com/aklump/json-schema-merge',
              ],
            ],
        ],
      ],
    ];
    $tests[] = [
      '{}',
      [],
    ];
    $tests[] = [
      '[]',
      [],
    ];

    return $tests;
  }

  /**
   * @dataProvider dataFortestLoadProvider
   */
  public function testLoadAndSave(string $json, array $expected) {
    $path_to_satis = $this->getTestFileFilepath('.cache/satis.json');

    $this->deleteTestFile($path_to_satis);
    $this->assertFileDoesNotExist($path_to_satis);

    $satis_manager = new SatisManager($path_to_satis);
    $this->assertEmpty($satis_manager->load(), 'Assert works when file does not exist.');

    $satis_manager->save(json_decode($json, TRUE));
    $packages = $satis_manager->load();
    $this->assertSame($expected, $packages, 'Assert works when file exists.');
  }
}
