<?php

namespace Ui\Controller;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\EventManager\EventManagerInterface;

use Engine\Storage\Factory;

abstract class UiController extends AbstractActionController {

    /**
     * @var Engine\Storage\Adapters\AdapterInterface
     */
    protected $storage;

    private function addLayoutEntity($type, $values) {
        $layout = $this->layout();
        $variables = $layout->getVariable($type);
        foreach ($values as $path) {
            if (strpos($path, 'http') !== 0) {
                $variables[] = "/static/$type/$path";
            } else {
                $variables[] = $path;
            }
        }
        $layout->setVariable($type, $variables);
    }

    protected function addCss() {
        $this->addLayoutEntity('css', func_get_args());
    }

    protected function addJs() {
        $this->addLayoutEntity('js', func_get_args());
    }

    public function setEventManager(EventManagerInterface $events) {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach(MvcEvent::EVENT_DISPATCH, function($e) use($controller) {
            $controller->init();
        }, 2);
    }

    protected function init() {        
        $config = $this->getServiceLocator()->get('Config');
        $this->storage = Factory::getAdapter($config['engine_storage']);
        $this->layout()->title = $config['view_manager']['title'];
        $this->layout()->js = array();
        $this->layout()->css = array();        
        $this->addCss('bootstrap.min.css', 'styles.css');
        $menu = array();
        $pages = $this->storage->read('pages');
        foreach ($pages->pages as $page) {
            if ($page->menu) {
                $menu[] = $page;
            }
        }
        $this->layout()->menu = $menu;
    }
}
