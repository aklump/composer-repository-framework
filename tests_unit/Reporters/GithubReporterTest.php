<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit\Reporters;

use AKlump\Packages\Reporters\GithubReporter;
use PHPUnit\Framework\TestCase;
use AKlump\ComposerRepositoryFramework\Tests\Unit\TestingTraits\TestWithFilesTrait;

/**
 * @covers \AKlump\Packages\Reporters\GithubReporter
 */
class GithubReporterTest extends TestCase {

  use TestWithFilesTrait;

  public function testGetVersionWhenBadResponseFromGitHub() {
    $version = (new GithubReporter())->getPackageVersion([]);
    $this->assertEmpty($version);
  }

  public function testGetVersionWhenNotTagsExist() {
    $github_reporter = $this->createPartialMock(GithubReporter::class, ['request']);
    $github_reporter->method('request')->willReturn('');
    $payload = $this->getTestFileFilepath('github/package.json');
    $request = json_decode(file_get_contents($payload), TRUE);
    $version = (new GithubReporter())->getPackageVersion($request);
    $this->assertEmpty($version);
  }

  public function testGetters() {
    $payload = $this->getTestFileFilepath('github/package.json');
    $request = json_decode(file_get_contents($payload), TRUE);

    $tags = $this->getTestFileFilepath('github/tags.json');
    $tags = file_get_contents($tags);
    $github_reporter = $this->createPartialMock(GithubReporter::class, ['request']);
    $github_reporter->method('request')->willReturn($tags);

    $this->assertNotEmpty($github_reporter->getName());
    $this->assertSame('aklump/json-schema-merge', $github_reporter->getPackageName($request));
    $this->assertSame('0.0.16', $github_reporter->getPackageVersion($request));

    $expected = [
      'type' => 'github',
      'url' => 'https://github.com/aklump/json-schema-merge',
    ];
    $this->assertSame($expected, $github_reporter->getRepositoryEntry($request));
  }

  public static function dataFortestShouldHandleProvider(): array {
    $tests = [];
    $tests[] = [
      [],
      FALSE,
    ];
    $tests[] = [
      [
        'sender' => [
          'url' => 'https://github.com/aklump/composer-repository-framework',
        ],
      ],
      TRUE,
    ];

    return $tests;
  }

  /**
   * @dataProvider dataFortestShouldHandleProvider
   */
  public function testShouldHandle(array $request, bool $expected) {
    $reporter = new GithubReporter();
    $this->assertSame($expected, $reporter->shouldHandle($request));
  }
}
