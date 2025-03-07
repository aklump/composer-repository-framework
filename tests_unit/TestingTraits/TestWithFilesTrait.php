<?php

namespace AKlump\ComposerRepositoryFramework\Tests\Unit\TestingTraits;

trait TestWithFilesTrait {

  /**
   * @param $test_file
   *   An absolute or relative path to a file in the test files directory to be deleted.  Absolute files must be in the test directory.
   *
   * @return void
   *
   * @see ::getTestFilesDirectory
   */
  public function deleteTestFile($test_file) {
    if (empty($test_file)) {
      throw new \InvalidArgumentException('$test_file cannot be empty');
    }
    $is_absolute = substr($test_file, 0, 1) === '/';
    if ($is_absolute && !$this->isTestFile($test_file)) {
      throw new \InvalidArgumentException(sprintf('You cannot delete absolute paths outside of the sandbox: %s', $test_file));
    }
    if (!$is_absolute) {
      $test_file = $this->getTestFileFilepath($test_file);
    }
    if (file_exists($test_file)) {
      if (is_dir($test_file)) {
        $this->deleteRecursively($test_file);
      }
      else {
        chmod($test_file, 0777);
        unlink($test_file);
      }
    }
  }

  private function isTestFile($path) {
    $test_dir = $this->getTestFilesDirectory();

    return strpos($path, $test_dir) === 0 || strpos($path, realpath($test_dir)) === 0;
  }

  private function getTestFilesDirectory() {
    $basepath = __DIR__ . '/../files/';
    if ($basepath && !file_exists($basepath)) {
      mkdir($basepath, 0755, TRUE);
    }
    else {
      chmod($basepath, 0755);
    }
    if (!$basepath || !is_writable($basepath)) {
      throw new \RuntimeException(sprintf('Failed to establish a sandbox base directory: %s', $basepath));
    }

    return rtrim(realpath($basepath), '/') . '/';
  }

  private function deleteRecursively($path) {
    if (!$this->isTestFile($path)) {
      throw new \RuntimeException(sprintf('$path is not in the files sandbox and cannot be deleted. %s', $path));
    }
    chmod($path, 0777);
    if (!is_dir($path)) {
      unlink($path);

      return;
    }
    $files = array_diff(scandir($path), ['.', '..']);
    foreach ($files as $file) {
      $this->deleteRecursively("$path/$file");
    }
    rmdir($path);
  }

  public function getTestFileFilepath($relative = '', $create = FALSE) {
    $basedir = $this->getTestFilesDirectory();
    if (empty($relative)) {
      return $basedir;
    }
    $path = $basedir . ltrim($relative, '/');
    $is_dir = substr($path, -1) === '/';

    if ($create && !$is_dir && !pathinfo($path, PATHINFO_EXTENSION) && substr($path, 0, 1) !== '.') {
      throw new \InvalidArgumentException(sprintf('When creating a test filepath directory, $relative must end with a forward slash, e.g. ->%s("%s", true)', __FUNCTION__, "$relative/"));
    }

    if ($is_dir) {
      if ($create && !file_exists($path)) {
        mkdir($path, 0755, TRUE);
      }
    }
    elseif ($create) {
      $parent = dirname($path);
      if (!file_exists($parent)) {
        mkdir($parent, 0755, TRUE);
      }
      if (!file_exists($path)) {
        touch($path);
      }
    }

    if (file_exists($path)) {
      $path = realpath($path);
    }

    return $path;
  }

  public function getCanonicalPath(string $path): string {
    $suffix = '';
    if (!file_exists($path)) {
      throw new \InvalidArgumentException(sprintf('$path does not exist: %s', $path));
    }
    if (is_file($path)) {
      $suffix = basename($path);
      $path = dirname($path);
    }
    $path = exec("cd \"$path\" && pwd -L");
    $path = rtrim($path, DIRECTORY_SEPARATOR);
    if (!empty($suffix)) {
      $path = $path . DIRECTORY_SEPARATOR . $suffix;
    }

    return $path;
  }

}
