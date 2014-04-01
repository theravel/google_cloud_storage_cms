<?php

namespace App\Controller;

use App\Library\Request;
use App\Library\Storage\Factory;

class BaseController {

    protected $config = array();
    
    /**
     * @var App\Library\Storage\Adapters\AdapterInterface;
     */
    protected $storage;

    /**
     * @var App\Library\Request
     */
    public $request;

    public $layout = array();
    public $data = array();
    public $renderJson = false;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function init(array $config) {
        $this->config = $config;
        $this->storage = Factory::getAdapter($config['engine_storage']);
        $this->layout = $config['layout'];
        $this->layout['js'] = array();
        $this->layout['css'] = array();
        $this->addCss('bootstrap.min.css', 'styles.css');
        $menu = array();
        $pages = $this->storage->read('pages');
        foreach ($pages->entities as $page) {
            if ($page->menu) {
                $menu[] = $page;
            }
        }
        $this->layout['menu'] = $menu;
    }

    private function addLayoutEntity($type, array $values) {
        foreach ($values as $path) {
            if (strpos($path, 'http') !== 0) {
                $this->layout[$type][] = "/static/$type/$path";
            } else {
                $this->layout[$type][] = $path;
            }
        }
    }

    protected function addCss() {
        $this->addLayoutEntity('css', func_get_args());
    }

    protected function addJs() {
        $this->addLayoutEntity('js', func_get_args());
    }

    protected function render($path) {
        list($controllerName, $actionName) = explode('/', $path);
        $this->request->controllerName = $controllerName;
        $this->request->actionName = $actionName;
    }

    public function notFoundAction() {
        header('HTTP/1.0 404 Not Found');
        $this->render('error/index');
    }
    
    public function __call($name, $args) {
        $this->notFoundAction();
    }

    public function redirect($url) {
        header("Location: $url");
        exit;
    }
}