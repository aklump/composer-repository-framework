<?php

namespace AKlump\Packages\API\Resource;

use AKlump\AnnotatedResponse\AnnotatedResponseInterface;

interface ResourceInterface {

  public function get(): AnnotatedResponseInterface;

  public function post(string $content): AnnotatedResponseInterface;

  public function delete(): AnnotatedResponseInterface;
}
