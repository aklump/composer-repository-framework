<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\Sender;

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
}
