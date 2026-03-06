<?php

namespace AKlump\Packages;

class SatisManager {

  private string $satis;

  const DEFAULTS = [
    'name' => '',
    'homepage' => '',
    'require-all' => TRUE,
    'repositories' => [],
  ];

  public function __construct(string $path_to_satis) {
    $this->satis = $path_to_satis;
  }

  /**
   * Load the satis.json file and return the raw data.
   */
  public function load(): array {
    $data = [];
    if (file_exists($this->satis)) {
      $data = json_decode(file_get_contents($this->satis), TRUE) ?? [];
    }
    $normalized = [];
    foreach (self::DEFAULTS as $key => $value) {
      $normalized[$key] = $data[$key] ?? $value;
    }

    return $normalized;
  }

  /**
   * Save raw data to the satis.json file.
   */
  public function save(array $data) {
    file_put_contents($this->satis, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  }

  /**
   * Remove a package from the satis.json file if it exists.
   *
   * @param string $package_url The package URL to remove.
   */
  public function remove(string $package_url): array {
    if (strpos($package_url, '://') === FALSE && strpos($package_url, 'git@') !== 0) {
      return [];
    }

    $satis_data = $this->load();
    if (empty($satis_data['repositories'])) {
      return [];
    }

    $removed_urls = [];
    $satis_data['repositories'] = array_filter($satis_data['repositories'], function ($repo) use ($package_url, &$removed_urls) {
      if (isset($repo['url']) && $repo['url'] === $package_url) {
        $removed_urls[] = $repo['url'];

        return FALSE;
      }

      return TRUE;
    });

    if (!empty($removed_urls)) {
      $this->save($satis_data);
    }

    return $removed_urls;
  }

}
