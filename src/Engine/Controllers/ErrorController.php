<?php

namespace Engine\Controllers;

class ErrorController {
    public function __call($name, $args) {
        echo '404';
    }
}