<?php

namespace Ui\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Ui\Models\Page;
use Ui\Models\Pages;

class AdminController extends UiController {

    protected function hash($string) {
        return hash('sha256', $string, false);
    }

    protected function init() {
        parent::init();
        $this->addCss('ui-lightness/jquery-ui-1.10.4.custom.min.css', 'admin.css');
        $this->addJs('jquery-1.10.2.js', 'jquery-ui-1.10.4.custom.min.js', 'admin.js');
        session_start();

        if (in_array($this->params('action'), array('login', 'logout'))) {
            return;
        }
        if (!isset($_SESSION['user'])) {
            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Location', '/admin/login');
            $response->setStatusCode(302);
            $response->sendHeaders();
        } else {
            $menu = array(                
                (object) array('url' => '/admin/pages',  'title' => 'Pages'),
                (object) array('url' => '/admin/users',  'title' => 'Users'),
                (object) array('url' => '/admin/logout', 'title' => 'Logout'),
            );
            $this->layout()->menu = $menu;
        }
    }

    public function indexAction() {
        return new ViewModel();
    }

    public function loginAction() {
        $invalidCredentials = false;
        if ($this->getRequest()->isPost()) {
            $users = $this->storage->read('users');
            $invalidCredentials = true;
            foreach ($users->entities as $user) {
                if ($user->login == $this->getRequest()->getPost('login') && $user->password == $this->hash($this->getRequest()->getPost('password'))) {
                    $_SESSION['user'] = $user;
                    return $this->redirect()->toUrl('/admin');
                }
            }
        }
        return new ViewModel(array(
            'invalidCredentials' => $invalidCredentials,
        ));
    }

    public function logoutAction() {
        session_destroy();
        return $this->redirect()->toUrl('/admin');
    }

    public function usersAction() {
        $users = $this->storage->read('users');
        return new ViewModel(array(
            'users' => $users->entities,
        ));
    }

    public function pagesAction() {
        $pages = $this->storage->read('pages');
        return new ViewModel(array(
            'pages' => $pages->entities,
        ));
    }

    public function pagesSortAction() {
        $order = $this->getRequest()->getPost('order');
        $pages = $this->storage->read('pages');
        $newPages = array();
        foreach ($order as $id) {
            foreach ($pages->entities as $page) {
                if ($page->id == $id) {
                    $newPages[] = $page;
                    break;
                }
            }
        }
        $model = new Pages;
        $model->entities = $newPages;
        return new JsonModel(array(
            'success' => $this->storage->write($model),
        ));
    }

    public function pagesMenuAction() {
        $id = $this->getRequest()->getPost('id');
        $enabled = $this->getRequest()->getPost('enabled') === 'true';
        $pages = $this->storage->read('pages');
        foreach ($pages->entities as &$page) {
            if ($page->id == $id) {
                $page->menu = $enabled;
                break;
            }
        }
        $model = new Pages;
        $model->entities = $pages->entities;
        return new JsonModel(array(
            'success' => $this->storage->write($model),
        ));
    }

    public function pagesDeleteAction() {
        $id = $this->getRequest()->getPost('id');
        $pages = $this->storage->read('pages');
        $model = new Pages;
        foreach ($pages->entities as $page) {
            if ($page->id != $id) {
                $model->entities[] = $page;
            }
        }
        return new JsonModel(array(
            'success' => $this->storage->write($model) && $this->storage->delete('page', $id),
        ));
    }

    public function pagesnewAction($checkUnique = true, Page $page = null) {
        $url = $this->getRequest()->getPost('url', $page ? $page->url : null);
        $title = $this->getRequest()->getPost('title', $page ? $page->title : null);
        $content = $this->getRequest()->getPost('content', $page ? $page->content : null);
        $errorMessage = null;
        
        if ($this->getRequest()->isPost()) {
            $page = new Page;
            $page->url = $url;
            $page->title = $title;
            $page->content = $content;
            if ($checkUnique) {
                $this->storage->validateUnique($page);
            }
            if ($page->validate()) {
                $this->storage->write($page);
                $pages = new Pages;
                $pages->entities = $this->storage->read('pages')->entities;
                $pages->entities[] = (object) array(
                    'id' => $url,
                    'url' => "/pages/$url",
                    'title' => $title,
                    'menu' => false,
                );
                $this->storage->write($pages);
                return $this->redirect()->toUrl('/admin/pages');
            } else {
                $errorMessage = $page->getValidationErrors(0);
            }
        }
        return new ViewModel(array(
            'errorMessage' => $errorMessage,
            'url' => $url,
            'title' => $title,
            'content' => $content,
        ));
    }

    public function pageseditAction() {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        if (!$this->storage->exists('page', $id)) {
            return $this->redirect()->toUrl('/admin/pages');
        }
        $model = new Page;
        $page = $this->storage->read('page', $id);
        foreach ($page as $key => $value) {
            $model->$key = $value;
        }
        return $this->pagesnewAction(false, $model);
    }
}