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
      '{"name":"aklump/packages","homepage":"https://packages.intheloftstudios.com"}',
      [
        'name' => 'aklump/packages',
        'homepage' => 'https://packages.intheloftstudios.com',
        'require-all' => TRUE,
        'repositories' => [],
      ],
    ];
    $tests[] = [
      '{"name":"aklump/packages","homepage":"https://packages.intheloftstudios.com","repositories":[{"type":"github","url":"https://github.com/aklump/json-schema-merge"}]}',
      [
        'name' => 'aklump/packages',
        'homepage' => 'https://packages.intheloftstudios.com',
        'require-all' => TRUE,
        'repositories' =>
          [
            [
              'type' => 'github',
              'url' => 'https://github.com/aklump/json-schema-merge',
            ],
          ],
      ],
    ];
    $tests[] = [
      '{"name":"aklump/packages","homepage":"https://packages.intheloftstudios.com","require-all":true,"repositories":[{"type":"github","url":"https://github.com/aklump/json-schema-merge"}]}',
      [
        'name' => 'aklump/packages',
        'homepage' => 'https://packages.intheloftstudios.com',
        'require-all' => TRUE,
        'repositories' =>
          [
            [
              'type' => 'github',
              'url' => 'https://github.com/aklump/json-schema-merge',
            ],
          ],
      ],
    ];
    $tests[] = [
      '{}',
      SatisManager::DEFAULTS,
    ];
    $tests[] = [
      '[]',
      SatisManager::DEFAULTS,
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
    $data = $satis_manager->load();
    $this->assertArrayHasKey('repositories', $data);
    $this->assertEmpty($data['repositories'], 'Assert works when file does not exist.');

    $satis_manager->save(json_decode($json, TRUE));
    $packages = $satis_manager->load();
    $this->assertSame($expected, $packages, 'Assert works when file exists.');
  }

  public function testRemove() {
    $path_to_satis = $this->getTestFileFilepath('.cache/satis.json');
    $this->deleteTestFile($path_to_satis);
    $satis_manager = new SatisManager($path_to_satis);

    $data = [
      'repositories' => [
        ['type' => 'github', 'url' => 'https://github.com/aklump/drupal_dev_sandbox'],
        ['type' => 'github', 'url' => 'https://github.com/aklump/json-schema-merge'],
      ],
    ];
    $satis_manager->save($data);

    // Test removal by name (WHICH SHOULD NOW BE DISABLED)
    $removed = $satis_manager->remove('aklump/drupal_dev_sandbox');
    $this->assertCount(0, $removed);

    $data = $satis_manager->load();
    $this->assertCount(2, $data['repositories']);

    // Test removal by full URL (WHICH SHOULD NOW WORK)
    $removed = $satis_manager->remove('https://github.com/aklump/json-schema-merge');
    $this->assertCount(1, $removed);
    $this->assertEquals('https://github.com/aklump/json-schema-merge', $removed[0]);

    $data = $satis_manager->load();
    $this->assertCount(1, $data['repositories']);
    $repository = array_values($data['repositories'])[0];
    $this->assertEquals('https://github.com/aklump/drupal_dev_sandbox', $repository['url']);
  }
}
