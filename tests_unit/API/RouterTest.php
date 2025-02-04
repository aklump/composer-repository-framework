<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit\API;

use AKlump\AnnotatedResponse\AnnotatedResponseInterface;
use AKlump\ComposerRepositoryFramework\Tests\Unit\TestingClasses\TestResource;
use AKlump\Packages\API\Resource\PackagesResource;
use AKlump\Packages\API\Resource\ResourceInterface;
use AKlump\Packages\API\ResourceRepository;
use AKlump\Packages\API\Router;
use AKlump\Packages\ChangeReporterRepository;
use AKlump\Packages\ChangeReporterRepositoryInterface;
use AKlump\Packages\HTTP\CreateError;
use AKlump\Packages\PackageChangeManager;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \AKlump\Packages\API\Router
 * @uses   \AKlump\Packages\API\Resource\PackagesResource
 * @uses   \AKlump\Packages\HTTP\CreateError
 * @uses   \AKlump\Packages\API\ResourceRepository
 */
class RouterTest extends TestCase {

  private $resourceRepository;

  /**
   * @var \Monolog\Logger|(\Monolog\Logger&\object&\PHPUnit\Framework\MockObject\MockObject)|(\Monolog\Logger&\PHPUnit\Framework\MockObject\MockObject)|(\object&\PHPUnit\Framework\MockObject\MockObject)|\PHPUnit\Framework\MockObject\MockObject
   */
  private $logger;

  /**
   * @var \AKlump\Packages\ChangeReporterRepository|(\AKlump\Packages\ChangeReporterRepository&\object&\PHPUnit\Framework\MockObject\MockObject)|(\AKlump\Packages\ChangeReporterRepository&\PHPUnit\Framework\MockObject\MockObject)|(\object&\PHPUnit\Framework\MockObject\MockObject)|\PHPUnit\Framework\MockObject\MockObject
   */
  private $changeReporterRepository;

  public function testPostSendsContentToControllerAndReturnsControllerResponse() {
    $router = $this->createRouter();
    $controller = $this->createMock(ResourceInterface::class);
    $content = '{"foo":"bar"}';
    $expected_response = $this->createMock(AnnotatedResponseInterface::class);
    $controller->expects($this->once())
      ->method('post')
      ->with($content)
      ->willReturn($expected_response);
    $this->resourceRepository
      ->method('getResourceController')
      ->willReturn($controller);

    $response = $router->handle('POST', 'packages', $content);
    $this->assertSame($expected_response, $response);
  }

  public function testGetCallsGetMethodOnControllerAndReturnsControllerResponse() {
    $router = $this->createRouter();
    $controller = $this->createMock(ResourceInterface::class);
    $expected_response = $this->createMock(AnnotatedResponseInterface::class);
    $controller->expects($this->once())
      ->method('get')
      ->willReturn($expected_response);
    $this->resourceRepository
      ->method('getResourceController')
      ->willReturn($controller);

    $response = $router->handle('GET', 'packages');
    $this->assertSame($expected_response, $response);
  }

  public function testDeleteCallsDeleteMethodOnControllerAndReturnsControllerResponse() {
    $router = $this->createRouter();
    $controller = $this->createMock(ResourceInterface::class);
    $expected_response = $this->createMock(AnnotatedResponseInterface::class);
    $controller->expects($this->once())
      ->method('delete')
      ->willReturn($expected_response);
    $this->resourceRepository
      ->method('getResourceController')
      ->willReturn($controller);

    $response = $router->handle('DELETE', 'packages');
    $this->assertSame($expected_response, $response);
  }

  public function testInvalidRouteErrors() {
    $router = $this->createRouter();
    $this->resourceRepository
      ->method('getResourceController')
      ->willReturn(NULL);
    $response = $router->handle('GET', 'packages');
    $this->assertSame(404, $response->getHttpStatus());
    $this->assertSame('Invalid route: packages', $response->jsonSerialize()['message']);
  }

  public function testInvalidRequestMethodErrors() {
    $router = $this->createRouter();
    $controller = $this->createMock(ResourceInterface::class);
    $this->resourceRepository->expects($this->once())
      ->method('getResourceController')
      ->willReturn($controller);
    $response = $router->handle('BOGUS', 'packages');
    $this->assertSame(405, $response->getHttpStatus());
    $this->assertSame('Invalid request method: BOGUS', $response->jsonSerialize()['message']);
  }

  public function testMethodSetChangeReporterRepositoryGetsCalled() {
    $router = $this->createRouter();
    $controller = $this->createMock(TestResource::class);
    $controller->expects($this->once())
      ->method('setChangeReporterRepository')
      ->with($this->changeReporterRepository);
    $this->resourceRepository
      ->method('getResourceController')
      ->willReturn($controller);
    $router->handle('GET', 'packages');
  }

  public function testControllerThrowsLogsExceptionAndReturns500() {
    $router = $this->createRouter();

    $controller = $this->createMock(ResourceInterface::class);
    $controller->method('get')
      ->willThrowException(new RuntimeException("jist can't do it captain!"));

    $this->resourceRepository
      ->method('getResourceController')
      ->willReturn($controller);

    $this->logger->expects($this->once())->method('error');

    $response = $router->handle('GET', 'packages');
    $this->assertSame(500, $response->getHttpStatus());
  }

  private function createRouter(): Router {
    $this->logger = $this->createMock(Logger::class);
    $package_change_manager = $this->createMock(PackageChangeManager::class);
    $this->changeReporterRepository = $this->createMock(ChangeReporterRepository::class);
    $this->resourceRepository = $this->createMock(ResourceRepository::class);

    return new Router($this->resourceRepository,
      $package_change_manager,
      $this->changeReporterRepository,
      $this->logger);
  }

}

