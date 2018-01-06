<?php

ini_set('display_errors', '1');
ini_set("date.timezone", "Africa/Nairobi");

error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

$loader = require 'vendor/autoload.php';
$loader->add('Strukt', __DIR__.'/src/');
$loader->add('Fixture', __DIR__.'/fixture/');