<?php

namespace App\Library;

use App\Controller\BaseController;
use App\Library\Routing\Router;
use App\Library\Translate;

class Bootstrap {

    protected $config;
    protected $viewExtension = 'phtml';

    public function __construct(array $config = array()) {
        $this->config = $config;
        Translate::setConfig($config['locale']);
    }

    public function run() {
        $router = new Router();
        $router->controller->init($this->config);
        $router->controller->{$router->action}();
        $this->render($router->controller);
    }

    protected function render(BaseController $controller) {
        $data = $controller->data;
        if ($controller->renderJson) {
            echo json_encode($data);
        } else {
            $t = function($id) {
                return Translate::t($id);
            };
            $layout = $controller->layoutData;
            $ext = $this->viewExtension;
            $prefix = dirname(__DIR__) . '/View';
            include "$prefix/helpers.php";
            $view = "$prefix/{$controller->request->controllerName}/{$controller->request->actionName}.$ext";
            if ($controller->layout) {
                include "$prefix/{$controller->layout}.$ext";
            } else {
                include $view;
            }
        }
    }
}