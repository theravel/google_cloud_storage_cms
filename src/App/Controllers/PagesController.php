<?php

namespace App\Controllers;

use Engine\Controllers\BaseController;
use Engine\Exceptions\RouteNotFoundException;

class PagesController extends BaseController {

    public function __call($name, $args) {
        if (!$this->storage->exists($name)) {
            throw new RouteNotFoundException('No such page');
        }
        echo $this->storage->read($name);
    }

}