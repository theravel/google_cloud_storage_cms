<?php

namespace Engine\Routing;

class Router {

    const DEFAULT_CONTROLLER = 'index';
    const DEFAULT_ACTION = 'index';
    const ERROR_CONTROLLER = 'error';
    const NOT_FOUND_ACTION = 'notFoundAction';

    public $controller;
    public $action;

    public function __construct() {
        $parts = explode('/', trim($_SERVER['REQUEST_URI'], ' /'));
        if (count($parts) == 1) {
            if (empty($parts[0])) {
                $parts[0] = self::DEFAULT_CONTROLLER;
            }
            $parts = array($parts[0], self::DEFAULT_ACTION);
        }
        list($controllerName, $actionName) = $parts;
        $controllerClass = '\App\Controllers\\' . ucfirst(strtolower($controllerName)) . 'Controller';
        if (!class_exists($controllerClass)) {
            $controllerClass = '\Engine\Controllers\\' . ucfirst(self::ERROR_CONTROLLER) . 'Controller';
        }
        $this->controller = new $controllerClass;
        $this->action = $actionName;
    }
}