<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\HTTP\Resources;

use AKlump\AnnotatedResponse\AnnotatedResponse;
use AKlump\AnnotatedResponse\AnnotatedResponseInterface;
use AKlump\Packages\HTTP\Error;
use AKlump\Packages\Schedule;
use AKlump\Packages\Sender\Github;

final class Packages {

  /**
   * @var \AKlump\Packages\Schedule
   */
  private Schedule $scheduler;

  public function __construct(Schedule $scheduler) {
    $this->scheduler = $scheduler;
  }

  public function get(): AnnotatedResponseInterface {
    $queue_path = $this->scheduler->getQueuePath();
    if (!file_exists($queue_path)) {
      return (new Error())(404, 'No changed files found.');
    }
    $data = json_decode(file_get_contents($queue_path), TRUE);
    $response = new AnnotatedResponse();

    return $response->setData($data);
  }

  public function post(string $raw_input): AnnotatedResponseInterface {
    if (empty($raw_input)) {
      return (new Error())(400, 'No input provided.');
    }

    $data = json_decode($raw_input, TRUE); // Decode JSON input into an associative array
    if (json_last_error() !== JSON_ERROR_NONE) {
      return (new Error())(400, 'Invalid JSON payload.');
    }

    $repositories = [];

    $github = new Github();
    if ($github->shouldHandle($data)) {
      $repositories[] = $github->getRepositoryEntry($data);
    }

    if (empty($repositories)) {
      return (new Error())(400, 'Unknown event sender.');
    }

    $was_added = $this->scheduler->add($repositories);
    if ($was_added) {
      $response = new AnnotatedResponse();
      $response->setHttpStatus(202)
        ->setMessage('Repository queued for update.')
        ->setData($repositories);

      return $response;
    }

    return (new Error())(400, 'Empty repositories; nothing to update.');
  }

  public function delete(): AnnotatedResponseInterface {
    if (file_exists($this->scheduler->getQueuePath())) {
      unlink($this->scheduler->getQueuePath());
    }
    $response = new AnnotatedResponse();
    $response->setHttpStatus(204)
      ->setMessage('Changed repository queue deleted.');

    return $response;
  }
}
