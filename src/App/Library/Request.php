<?php

namespace App\Library;

class Request {

    public $controllerName;
    public $actionName;
    public $params;

    public function isPost() {
        return !empty($_POST);
    }

    public function getPost($name, $default = null) {
        return isset($_POST[$name]) ? $_POST[$name] : $default;
    }
}
