<?php

$loader = include 'vendor/autoload.php';
$loader->add('Zend', 'vendor/zendframework/zendframework/library');
$loader->add('App', 'src');

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    var_dump('err', $errno, $errstr, $errfile, $errline); exit;
});

set_exception_handler(function($exception) {
    var_dump('ex', $exception); exit;
});

$bootstrap = new App\Library\Bootstrap(require 'config/ui.module.php');
$bootstrap->run();