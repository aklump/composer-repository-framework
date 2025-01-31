<?php

namespace InTheLoftStudios\Packages\Sender;

interface EventSenderInterface {

  /**
   * @param array $request The request data containing sender/repository info.
   *
   * @return bool  True if this class can handle the request.  This is
   * non-exclusive and makes no claim on other classes handling the same
   * request.
   */
  public function shouldHandle(array $request): bool;

  /**
   * @param array $request The request data containing sender/repository info.
   *
   * @return array Array to add to "repositories" in composer.json.
   *
   */
  public function getRepositoryEntry(array $request): array;
}
