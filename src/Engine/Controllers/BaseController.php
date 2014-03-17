<?php

namespace Engine\Controllers;

use Engine\Storage\Factory;

class BaseController {

    /**
     * @var Engine\Storage\StorageInterface
     */
    protected $storage;

    public function __construct() {
        $this->storage = Factory::getCurrentAdapter();
    }

    public function notFoundAction() {
        echo '404';
    }
    
    public function __call($name, $args) {
        echo 'call';
    }
}