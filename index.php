<?php

$loader = include 'vendor/autoload.php';
$loader->add('Engine', 'src');
$loader->add('App', 'src');

$bootstrap = new Engine\Bootstrap();
$bootstrap->run();