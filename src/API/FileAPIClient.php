<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\API;

use RuntimeException;

/**
 * Class Client
 *
 * This class is responsible for interacting with the repository server,
 * handling operations like reading the new-release list and flushing
 * repository changes via HTTP requests.
 *
 * @package AKlump\PackagesITLS
 */
final class FileAPIClient {

  private string $fileAPIServerPath;

  public function __construct(string $file_api_server_path) {
    $this->fileAPIServerPath = $file_api_server_path;
  }

  private function call(string $method, string $route, array $data = []): array {
    $command = sprintf('%s %s %s', $this->fileAPIServerPath, strtoupper($method), $route);
    $json_response = (string) exec($command);

    return json_decode($json_response, TRUE) ?? [];
  }

  /**
   * @return array Any repositories that reported new releases.
   */
  public function getPackages(): array {
    return $this->call('GET', 'packages')['data'] ?? [];
  }

  /**
   * Remove all repositories from the new-release list.
   *
   * @return void
   */
  public function markPackagesReceived(): void {
    $response = $this->call('DELETE', 'packages');
    if ('succeeded' !== ($response['result'] ?? '')) {
      throw new RuntimeException('Failed to send DELETE request.');
    }
  }
}
