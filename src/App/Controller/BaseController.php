<?php

namespace App\Controller;

use App\Library\Request;
use App\Library\Storage\Factory;
use App\Library\Translate;

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

    public $layout = 'layout/layout';
    public $layoutData = array();
    public $data = array();
    public $renderJson = false;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function init(array $config) {
        $this->config = $config;
        $this->storage = Factory::getAdapter($config['engine_storage']);
        $this->layoutData['js'] = array();
        $this->layoutData['css'] = array();        
        $this->addCss('bootstrap.min.css', 'styles.css');
        $this->addJs('jquery-1.10.2.js',  'main.js');

        $settings = $this->storage->read('settings');
        $this->layoutData['title'] = $settings->title;
        $this->layoutData['name'] = $settings->title;
        $this->layoutData['menu'] = $settings->entities;
        $this->layoutData['news'] = $this->getNews();
    }

    private function addLayoutEntity($type, array $values) {
        foreach ($values as $path) {
            if (strpos($path, 'http') !== 0) {
                $this->layoutData[$type][] = "/static/$type/$path";
            } else {
                $this->layoutData[$type][] = $path;
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

    protected function t($id) {
        return Translate::t($id);
    }

    public function notFoundAction() {
        header('HTTP/1.0 404 Not Found');
        $this->render('error/index');
    }
    
    protected function getNews() {
        $result = array();
        foreach ($this->storage->read('pages')->entities as $page) {
            if ($page->news) {
                $result[] = $page;
            }
        }
        return array_slice($result, 0, $this->config['news']['max_size']);
    }

    public function __call($name, $args) {
        $this->notFoundAction();
    }

    public function redirect($url) {
        header("Location: $url");
        exit;
    }
}