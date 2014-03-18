<?php

$loader = include 'vendor/autoload.php';
$loader->add('Zend', 'vendor/zendframework/zendframework/library');
$loader->add('Engine', 'src');
$loader->add('Ui', 'src/modules');

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    var_dump('err', $errno, $errstr, $errfile, $errline); exit;
});

set_exception_handler(function($exception) {
    var_dump('ex', $exception); exit;
});

Zend\Mvc\Application::init(require 'config/ui.module.php')->run();