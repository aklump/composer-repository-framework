<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit\HTTP;

use AKlump\Packages\HTTP\CreateError;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\Packages\HTTP\CreateError
 */
class CreateErrorTest extends TestCase {

  public function testInvoke() {
    $error = (new CreateError())(404, 'Ikkje her');
    $this->assertSame(404, $error->getHttpStatus());
    $this->assertSame('Ikkje her', $error->jsonSerialize()['message']);;
  }
}
