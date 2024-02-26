<?php

use Phalcon\Autoload\Loader;

$loader = new Loader();
$loader->setNamespaces(
  [
            'MyApp\Models' => __DIR__ . '/../models/',
            'MyApp\Services' => __DIR__ . '/../services/',
            'MyApp\Controllers' => __DIR__ . '/../controllers/',
  ]
);

$loader->register();