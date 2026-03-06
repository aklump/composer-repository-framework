<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit\HTTP;

use AKlump\Packages\HTTP\Authenticate;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \AKlump\Packages\HTTP\Authenticate
 */
class AuthenticateTest extends TestCase {

  private string $secret = 'test_secret';

  private string $body = '{"foo":"bar"}';

  public function testInvokeWithSha256Succeeds() {
    $auth = new Authenticate($this->secret);
    $signature = 'sha256=' . hash_hmac('sha256', $this->body, $this->secret);
    $headers = ['X-HUB-SIGNATURE-256' => $signature];

    $auth($headers, $this->body);
    $this->assertTrue(TRUE); // Should not throw exception
  }

  public function testInvokeWithSha1Succeeds() {
    $auth = new Authenticate($this->secret);
    $signature = 'sha1=' . hash_hmac('sha1', $this->body, $this->secret);
    $headers = ['X-HUB-SIGNATURE' => $signature];

    $auth($headers, $this->body);
    $this->assertTrue(TRUE); // Should not throw exception
  }

  public function testInvokeWithMissingHeadersThrows400() {
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Missing or empty X-HUB-SIGNATURE parameter');
    $this->expectExceptionCode(400);

    $auth = new Authenticate($this->secret);
    $auth([], $this->body);
  }

  public function testInvokeWithEmptyHeaderThrows400() {
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Missing or empty X-HUB-SIGNATURE parameter');
    $this->expectExceptionCode(400);

    $auth = new Authenticate($this->secret);
    $auth(['X-HUB-SIGNATURE-256' => ' '], $this->body);
  }

  public function testInvokeWithInvalidSignatureThrows403() {
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Invalid secret parameter');
    $this->expectExceptionCode(403);

    $auth = new Authenticate($this->secret);
    $headers = ['X-HUB-SIGNATURE-256' => 'sha256=invalid'];
    $auth($headers, $this->body);
  }

  public function testInvokeCaseInsensitiveHeadersSucceeds() {
    $auth = new Authenticate($this->secret);
    $signature = 'sha256=' . hash_hmac('sha256', $this->body, $this->secret);
    $headers = ['x-hub-signature-256' => $signature];

    $auth($headers, $this->body);
    $this->assertTrue(TRUE); // Should not throw exception
  }
}
