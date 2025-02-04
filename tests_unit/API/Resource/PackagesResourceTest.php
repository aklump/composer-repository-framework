<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit\API\Resource;

use AKlump\AnnotatedResponse\AnnotatedResponseInterface;
use AKlump\ComposerRepositoryFramework\Tests\Unit\TestingTraits\TestWithFilesTrait;
use AKlump\Packages\API\Resource\PackagesResource;
use AKlump\Packages\ChangeReporterRepository;
use AKlump\Packages\PackageChangeManager;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use AKlump\Packages\Reporters\ChangeReporterInterface;

/**
 * @covers \AKlump\Packages\API\Resource\PackagesResource
 * @uses   \AKlump\Packages\HTTP\CreateError
 * @uses   \AKlump\Packages\Reporters\GithubReporter
 * @uses   \AKlump\Packages\ChangeReporterRepository::get
 */
class PackagesResourceTest extends TestCase {

  use TestWithFilesTrait;

  public static function dataFortestGetProvider(): array {
    $tests = [];
    $tests[] = [
      [],
      ['code' => 404, 'data' => []],
    ];
    $tests[] = [
      [
        [
          'date' => '2025-02-03T18:03:57+00:00',
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
      [
        'code' => 200,
        'data' => [
          [
            'date' => '2025-02-03T18:03:57+00:00',
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
      ],
    ];

    return $tests;
  }

  /**
   * @dataProvider dataFortestGetProvider
   */
  public function testGet(array $changed_packages, array $expected) {
    $logger = $this->createMock(Logger::class);
    $package_change_manager = $this->createConfiguredMock(PackageChangeManager::class, [
      'getChangedPackages' => $changed_packages,
    ]);
    $resource = new PackagesResource($package_change_manager, $logger);

    $response = $resource->get();
    $this->assertInstanceOf(AnnotatedResponseInterface::class, $response);

    $data = json_decode(json_encode($response), TRUE)['data'];
    $this->assertSame($expected['code'], $response->getHttpStatus());
    $this->assertSame($expected['data'], $data);
  }

  public function testDelete() {
    $logger = $this->createMock(Logger::class);
    $package_change_manager = $this->createMock(PackageChangeManager::class);
    $package_change_manager->expects($this->once())->method('clearAll');
    $resource = new PackagesResource($package_change_manager, $logger);
    $response = $resource->delete();
    $this->assertInstanceOf(AnnotatedResponseInterface::class, $response);
    $this->assertSame(204, $response->getHttpStatus());
  }

  public function testPostEmptyRawInput() {
    $logger = $this->createMock(Logger::class);
    $package_change_manager = $this->createMock(PackageChangeManager::class);
    $response = (new PackagesResource($package_change_manager, $logger))->post('');
    $this->assertInstanceOf(AnnotatedResponseInterface::class, $response);
    $this->assertSame(406, $response->getHttpStatus());
  }

  public function testPostInvalidJSONRawInput() {
    $logger = $this->createMock(Logger::class);
    $package_change_manager = $this->createMock(PackageChangeManager::class);
    $response = (new PackagesResource($package_change_manager, $logger))->post('lorem }');
    $this->assertInstanceOf(AnnotatedResponseInterface::class, $response);
    $this->assertSame(406, $response->getHttpStatus());
  }

  public function testPostWasAdded() {
    $payload = $this->getTestFileFilepath('github/package.json');
    $payload = file_get_contents($payload);

    $logger = $this->createMock(Logger::class);
    $logger->expects($this->once())->method('info');

    $package_change_manager = $this->createMock(PackageChangeManager::class);
    $package_change_manager->expects($this->once())
      ->method('reportChanges')
      ->willReturn(TRUE);

    $resource = new PackagesResource($package_change_manager, $logger);

    $data = json_decode($payload, TRUE);
    $reporter = $this->createConfiguredMock(ChangeReporterInterface::class, [
      'shouldHandle' => TRUE,
      'getPackageName' => $data['repository']['full_name'],
      'getRepositoryEntry' => ['url' => $data['repository']['html_url']],
    ]);
    $repository = $this->createConfiguredMock(ChangeReporterRepository::class, [
      'get' => [
        $reporter,
      ],
    ]);
    $resource->setChangeReporterRepository($repository);

    $response = $resource->post($payload);
    $this->assertInstanceOf(AnnotatedResponseInterface::class, $response);
    $this->assertSame(202, $response->getHttpStatus());
    $this->assertSame('aklump/json-schema-merge', $response->jsonSerialize()['data'][0]['name']);
  }

  public function testPostFromUnknown() {
    $logger = $this->createMock(Logger::class);
    $package_change_manager = $this->createMock(PackageChangeManager::class);
    $resource = new PackagesResource($package_change_manager, $logger);
    $repository = $this->createConfiguredMock(ChangeReporterRepository::class, [
      'get' => [],
    ]);
    $resource->setChangeReporterRepository($repository);
    $payload = '{"foo":"bar"}';
    $response = $resource->post($payload);
    $this->assertInstanceOf(AnnotatedResponseInterface::class, $response);
    $this->assertSame(400, $response->getHttpStatus());
  }

  public function testPostFailedToSaveThePayload() {
    $logger = $this->createMock(Logger::class);
    $package_change_manager = $this->createConfiguredMock(PackageChangeManager::class, [
      'reportChanges' => FALSE,
    ]);
    $resource = new PackagesResource($package_change_manager, $logger);
    $reporter = $this->createConfiguredMock(ChangeReporterInterface::class, [
      'shouldHandle' => TRUE,
      'getName' => 'Goo Reporter',
      'getPackageName' => 'aklump/goo',
      'getPackageVersion' => '1.0.0',
      'getRepositoryEntry' => ['url' => 'https://github.com/aklump/goo'],
    ]);
    $repository = $this->createConfiguredMock(ChangeReporterRepository::class, [
      'get' => [
        $reporter,
      ],
    ]);
    $resource->setChangeReporterRepository($repository);
    $payload = '{"foo":"bar"}';
    $response = $resource->post($payload);
    $this->assertInstanceOf(AnnotatedResponseInterface::class, $response);
    $this->assertSame(500, $response->getHttpStatus());
  }
}
