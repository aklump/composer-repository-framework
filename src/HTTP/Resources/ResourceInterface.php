<?php

namespace AKlump\Packages\HTTP\Resources;

use Monolog\Logger;

interface ResourceInterface {

  public function getLogger(): Logger;
}
