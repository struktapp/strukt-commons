<?php

/**
 * Loads Composer's generated autoloader for the Strukt Commons package.
 *
 * @throws RuntimeException When Composer dependencies have not been installed.
 */

$autoload = __DIR__ . '/vendor/autoload.php';

if (!is_file($autoload)) {
    throw new RuntimeException('Composer dependencies are not installed. Run composer install first.');
}

require_once $autoload;
