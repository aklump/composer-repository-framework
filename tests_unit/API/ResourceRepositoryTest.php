<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit\API;

use AKlump\Packages\API\Resource\PackagesResource;
use AKlump\Packages\API\ResourceRepository;
use AKlump\Packages\PackageChangeManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\Packages\API\ResourceRepository
 * @uses   \AKlump\Packages\API\Resource\PackagesResource
 */
class ResourceRepositoryTest extends TestCase {

  public function testControllerReceivesConstructorArguments() {
    $package_change_manager = $this->createMock(PackageChangeManager::class);
    $logger = $this->createMock(\Psr\Log\LoggerInterface::class);
    $result = (new ResourceRepository())->getResourceController('packages', $package_change_manager, $logger);
    $this->assertInstanceOf(PackagesResource::class, $result);
  }

  public function testBadRouteReturnsNull() {
    $result = (new ResourceRepository())->getResourceController('bogus');
    $this->assertNull($result);
  }
}
