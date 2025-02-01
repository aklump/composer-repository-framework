<?php

namespace AKlump\PackagesITLS\HTTP;

use AKlump\PackagesITLS\Config\GetQueueFile;
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
final class Client {

  private string $secret;

  private string $url;

  public function __construct(string $server_url, string $api_secret, string $cache_uri) {
    $this->url = $server_url;
    $this->secret = $api_secret;
    $this->cacheURI = $cache_uri;
  }

  /**
   * @return array Any repositories that reported new releases.
   */
  public function read(): array {
    $queue_file = $this->url . '/api/.cache/changed.json';
    if (!($queue_json = @file_get_contents($queue_file))) {
      return [];
    }

    return json_decode($queue_json, TRUE) ?? [];
  }

  /**
   * Remove all repositories from the new-release list.
   *
   * @return void
   */
  public function flush(): void {
    $url = $this->url . '/api/flush.php?secret=' . $this->secret;

    $options = [
      'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'DELETE',
        'content' => '{}',
      ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, FALSE, $context);

    if ($result === FALSE) {
      throw new RuntimeException('Failed to send DELETE request.');
    }
  }
}
