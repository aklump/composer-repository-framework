<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit;

use AKlump\Packages\Config\Constants;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\Packages\Config\Constants
 */
class ConstantsTest extends TestCase {

  public function testCacheConstant() {
    $this->assertNotEmpty(Constants::ROOT_RELATIVE_CACHE_PATH);
  }

  public function testUserAgent() {
    $this->assertNotEmpty(Constants::USER_AGENT);
  }
}
