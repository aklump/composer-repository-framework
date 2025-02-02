<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\Sender;

use AKlump\Packages\Config;

class Github implements EventSenderInterface {

  public function shouldHandle(array $request): bool {
    return !empty($request['repository']['html_url']) && strstr($request['sender']['url'], 'github.com');
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
    $tags_url = $request['repository']['tags_url'] ?? '';
    $response = $this->doGithubGetRequest($tags_url);
    if (!$response || !($tags = json_decode($response, TRUE)) || !is_array($tags)) {
      $tags = [];
    }

    return $this->getHighestVersion($tags);
  }


  private function getHighestVersion(array $tags) {
    $versions = array_map(fn($tag) => $tag['name'], $tags);
    uasort($versions, 'version_compare');

    return end($versions);
  }

  /**
   * Sends a GET request to the specified URL using GitHub API-specific options.
   *
   * @param string $url
   *   The URL to send the GET request to.
   *
   * @return string
   *   The response body as a string, or an empty string if the request fails.
   */
  private function doGithubGetRequest($url): string {
    $options = array(
      'http' => array(
        'method' => "GET",
        'header' => "User-Agent: " . Config::USER_AGENT,
        'timeout' => 2.0,
      ),
    );
    $context = stream_context_create($options);

    return file_get_contents($url, FALSE, $context);
  }
}
