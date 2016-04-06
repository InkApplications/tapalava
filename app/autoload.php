<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/php/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
