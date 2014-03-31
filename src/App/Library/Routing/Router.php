<?php

namespace App\Library\Routing;

use App\Library\Request;

class Router {

    const DEFAULT_CONTROLLER = 'pages';
    const DEFAULT_ACTION = 'index';
    const ERROR_CONTROLLER = 'error';
    const NOT_FOUND_ACTION = 'notFoundAction';

    /**
     * @var App\Controller\BaseController
     */
    public $controller;
    public $action;

    /**
     * @var App\Library\Request
     */
    public $request;

    public function __construct() {
        list($url) = explode('?', $_SERVER['REQUEST_URI']);
        $parts = explode('/', trim($url, ' /'));
        if (count($parts) == 1) {
            if (empty($parts[0])) {
                $parts[0] = self::DEFAULT_CONTROLLER;
            }
            $parts = array($parts[0], self::DEFAULT_ACTION);
        }
        list($controllerName, $actionName) = $parts;
        $this->request = new Request;
        $this->request->controllerName = strtolower($controllerName);
        $this->request->actionName = strtolower($actionName);
        $this->request->params = $parts;
        $controllerClass = '\App\Controller\\' . ucfirst($this->request->controllerName) . 'Controller';
        if (!class_exists($controllerClass)) {
            $controllerClass = '\App\Controller\\' . ucfirst(self::ERROR_CONTROLLER) . 'Controller';
            $this->request->controllerName = self::ERROR_CONTROLLER;
        }
        $this->controller = new $controllerClass($this->request);
        $this->action = $this->request->actionName . 'Action';
    }
}