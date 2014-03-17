<?php

namespace Engine;

use Engine\Routing\Router;
use Engine\Exceptions\RouteNotFoundException;

class Bootstrap {

    public function run() {
        $router = new Router();
        try {
            $router->controller->{$router->action}();
        } catch (RouteNotFoundException $e) {
            $router->controller->{Router::NOT_FOUND_ACTION}();
        }
    }
}