<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\API\Resource;

use AKlump\AnnotatedResponse\AnnotatedResponse;
use AKlump\AnnotatedResponse\AnnotatedResponseInterface;
use AKlump\Packages\ChangeReporterRepositoryInterface;
use AKlump\Packages\HTTP\CreateError;
use AKlump\Packages\PackageChangeManager;
use DateTimeInterface;
use Psr\Log\LoggerInterface;

final class PackagesResource implements ResourceInterface {

  private PackageChangeManager $packageChangeManager;

  private LoggerInterface $logger;

  private ChangeReporterRepositoryInterface $changeReporterRepository;

  public function __construct(
    PackageChangeManager $package_change_manager,
    LoggerInterface $logger
  ) {
    $this->packageChangeManager = $package_change_manager;
    $this->logger = $logger;
  }

  public function setChangeReporterRepository(ChangeReporterRepositoryInterface $change_reporter_repository): self {
    $this->changeReporterRepository = $change_reporter_repository;

    return $this;
  }

  public function get(): AnnotatedResponseInterface {
    $data = $this->packageChangeManager->getChangedPackages();
    if (empty($data)) {
      return (new CreateError())(404, 'No changed files found.');
    }

    return (new AnnotatedResponse())->setData($data);
  }

  public function post(string $raw_input): AnnotatedResponseInterface {
    if (empty($raw_input)) {
      return (new CreateError())(406, 'No input provided.');
    }

    $data = json_decode($raw_input, TRUE);
    if (json_last_error() !== JSON_ERROR_NONE) {
      return (new CreateError())(406, 'Invalid JSON payload.');
    }

    $entries = [];

    foreach ($this->changeReporterRepository->get() as $reporter) {
      if ($reporter->shouldHandle($data)) {
        $entry = [
          'date' => date_create('now', timezone_open('UTC'))->format(DateTimeInterface::ATOM),
          'name' => $reporter->getPackageName($data),
          'version' => $reporter->getPackageVersion($data),
          'repository' => $reporter->getRepositoryEntry($data),
        ];
        $entries[] = $entry;
        $this->logger->info(sprintf('%s webhook received', $reporter->getName()), [
          'name' => $entry['name'],
          'version' => $entry['version'],
        ]);
      }
    }

    if (empty($entries)) {
      return (new CreateError())(400, 'Unknown event sender.');
    }

    $was_added = $this->packageChangeManager->reportChanges($entries);
    if ($was_added) {
      $response = new AnnotatedResponse();
      $response->setHttpStatus(202)
        ->setMessage('Repository queued for update.')
        ->setData($entries);

      return $response;
    }

    return (new CreateError())(500, 'Request was not received; try again later.');
  }

  public function delete(): AnnotatedResponseInterface {
    $this->packageChangeManager->clearAll();

    $response = new AnnotatedResponse();
    $response->setHttpStatus(204)
      ->setMessage('Changed repository queue deleted.');

    return $response;
  }

}
