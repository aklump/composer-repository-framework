<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\Reporters;

use AKlump\Packages\Config\Constants;

class GithubReporter implements ChangeReporterInterface {

  public function shouldHandle(array $request): bool {
    return !empty($request['sender']['url']) && strstr($request['sender']['url'], 'github.com');
  }

  public function getRepositoryEntry(array $request): array {
    return [
      'type' => 'github',
      'url' => $request['repository']['html_url'],
    ];
  }

  public function getPackageName(array $request): string {
    return $request['repository']['full_name'] ?? '';
  }

  public function getPackageVersion(array $request): string {
    $tags = $this->getTags($request);

    return $this->getHighestVersion($tags);
  }

  private function getTags(array $request): array {
    if (empty($request['repository']['tags_url'])) {
      return [];
    }
    $response = $this->request($request['repository']['tags_url']);
    if (!$response || !($tags = json_decode($response, TRUE)) || !is_array($tags)) {
      $tags = [];
    }

    return $tags;
  }


  private function getHighestVersion(array $tags): string {
    $versions = array_column($tags, 'name');
    uasort($versions, 'version_compare');

    return end($versions);
  }


  public function getName(): string {
    return 'GitHub';
  }

  protected function request(string $url): string {
    $options = array(
      'http' => array(
        'method' => "GET",
        'header' => "User-Agent: " . Constants::USER_AGENT,
        'timeout' => 2.0,
      ),
    );
    $context = stream_context_create($options);

    return file_get_contents($url, FALSE, $context);
  }

}
