<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit;

use AKlump\ComposerRepositoryFramework\Tests\Unit\TestingTraits\TestWithFilesTrait;
use AKlump\Packages\PackageChangeManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\Packages\PackageChangeManager
 * @uses   \AKlump\Packages\Helper\DedupePackages
 */
class PackageChangeManagerTest extends TestCase {

  use TestWithFilesTrait;

  public static function dataFortestReportAndGetChangesProvider(): array {
    $tests = [];
    $tests[] = ['[{"date":"2025-02-03T18:03:57+00:00","name":"aklump/json-schema-merge","version":"","repository":{"type":"github","url":"https://github.com/aklump/json-schema-merge"}}]'];
    $tests[] = ['{}'];
    $tests[] = ['[]'];

    return $tests;
  }

  public function testClearAll() {
    $cache_dir = $this->getTestFileFilepath('.cache/pcm/');
    $this->deleteTestFile($cache_dir);
    $this->assertDirectoryDoesNotExist($cache_dir);
    $pcm = new PackageChangeManager($cache_dir);
    $json = '[{"date":"2025-02-03T18:03:57+00:00","name":"aklump/json-schema-merge","version":"","repository":{"type":"github","url":"https://github.com/aklump/json-schema-merge"}}]';
    $changed_packages = json_decode($json, TRUE);
    $pcm->reportChanges($changed_packages);
    $pcm->clearAll();
    $this->assertEmpty($pcm->getChangedPackages());
    $pcm->clearAll();
    $this->assertEmpty($pcm->getChangedPackages(), 'Assert request when file does not exist works fine.');
  }

  /**
   * @dataProvider dataFortestReportAndGetChangesProvider
   */
  public function testReportAndGetChanges(string $json) {
    $cache_dir = $this->getTestFileFilepath('.cache/pcm/');
    $this->deleteTestFile($cache_dir);
    $this->assertDirectoryDoesNotExist($cache_dir);
    $pcm = new PackageChangeManager($cache_dir);
    $changed_packages = json_decode($json, TRUE);
    $this->assertTrue($pcm->reportChanges($changed_packages));
    $result = $pcm->getChangedPackages();
    $this->assertSame($changed_packages, $result);
  }

  public function testReportChangesWithMerge() {
    $cache_dir = $this->getTestFileFilepath('.cache/pcm/');
    $this->deleteTestFile($cache_dir);
    $this->assertDirectoryDoesNotExist($cache_dir);
    $pcm = new PackageChangeManager($cache_dir);

    $changes = [
      '[{"date":"2025-02-03T18:03:57+00:00","name":"aklump/json-schema-merge","version":"","repository":{"type":"github","url":"https://github.com/aklump/json-schema-merge"}}]',
      '[{"date":"2025-02-04T18:03:57+00:00","name":"aklump/json-schema-merge","version":"","repository":{"type":"github","url":"https://github.com/aklump/json-schema-merge"}}]',
      '[{"date":"2025-02-04T20:03:57+00:00","name":"aklump/easy-perms","version":"","repository":{"type":"github","url":"https://github.com/aklump/easy-perms"}}]',
    ];

    foreach ($changes as $change) {
      $change = json_decode($change, TRUE);
      $this->assertTrue($pcm->reportChanges($change));
    }

    $result = $pcm->getChangedPackages();
    $this->assertCount(2, $result);
    $this->assertSame('aklump/easy-perms', $result[0]["name"]);

    $this->assertSame('aklump/json-schema-merge', $result[1]["name"]);
    $this->assertSame('2025-02-04T18:03:57+00:00', $result[1]["date"], 'Assert most recent change is kept during dedupe.');
  }

  public function testGetChangesOnEmpty() {
    $cache_dir = $this->getTestFileFilepath('.cache/pcm/');
    $this->deleteTestFile($cache_dir);
    $this->assertDirectoryDoesNotExist($cache_dir);
    $pcm = new PackageChangeManager($cache_dir);
    $result = $pcm->getChangedPackages();
    $this->assertIsArray($result);
    $this->assertEmpty($result);
  }

}
