<?php

namespace App\Controller;

use App\Models\Page;
use App\Models\Pages;
use App\Models\Users;

use App\Library\Translate;

class AdminController extends BaseController {

    protected function hash($string) {
        return hash('sha256', $string, false);
    }

    public function init(array $config) {
        parent::init($config);
        $this->addCss('ui-lightness/jquery-ui-1.10.4.custom.min.css', 'admin.css');
        $this->addJs('jquery-1.10.2.js', 'jquery-ui-1.10.4.custom.min.js', 'admin.js');
        session_start();

        if (in_array($this->request->actionName, array('login', 'logout'))) {
            return;
        }
        if (!isset($_SESSION['user'])) {
            $this->redirect('/admin/login');
        } else {
            $menu = array(                
                (object) array('url' => '/admin/pages',  'title' => 'Pages'),
                (object) array('url' => '/admin/users',  'title' => 'Users'),
                (object) array('url' => '/admin/logout', 'title' => 'Logout'),
            );
            $this->layout['menu'] = $menu;
        }
    }

    public function indexAction() {
        ;
    }

    public function loginAction() {
        $invalidCredentials = false;
        if ($this->request->isPost()) {
            $users = $this->storage->read('users');
            $invalidCredentials = true;
            foreach ($users->entities as $user) {
                if ($user->login == $this->request->getPost('login') && $user->password == $this->hash($this->request->getPost('password'))) {
                    $_SESSION['user'] = $user;
                    $this->redirect('/admin');
                }
            }
        }
        $this->data['invalidCredentials'] = $invalidCredentials;
    }

    public function logoutAction() {
        session_destroy();
        $this->redirect('/admin');
    }

    public function usersAction() {
        $users = $this->storage->read('users');
        $this->data['users'] = $users->entities;
    }

    public function pagesAction() {
        $pages = $this->storage->read('pages');
        $this->data['pages'] = $pages->entities;
    }

    public function pagesSortAction() {
        $order = $this->request->getPost('order');
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
        $this->data['success'] = $this->storage->write($model);
        $this->renderJson = true;
    }

    public function pagesMenuAction() {
        $id = $this->request->getPost('id');
        $enabled = $this->request->getPost('enabled') === 'true';
        $pages = $this->storage->read('pages');
        foreach ($pages->entities as &$page) {
            if ($page->id == $id) {
                $page->menu = $enabled;
                break;
            }
        }
        $model = new Pages;
        $model->entities = $pages->entities;
        $this->data['success'] = $this->storage->write($model);        
        $this->renderJson = true;
    }

    public function pagesDeleteAction() {
        $id = $this->request->getPost('id');
        $pages = $this->storage->read('pages');
        $model = new Pages;
        foreach ($pages->entities as $page) {
            if ($page->id != $id) {
                $model->entities[] = $page;
            }
        }
        $this->data['success'] = $this->storage->write($model) && $this->storage->delete('page', $id);
        $this->renderJson = true;
    }

    public function pagesnewAction($checkUnique = true, Page $page = null) {
        if ($checkUnique) {
            $this->addJs(
                'ckeditor/ckeditor.js',
                'ckeditor/adapters/jquery.js'
            );
        }
        $url = $this->request->getPost('url', $page ? $page->url : null);
        $title = $this->request->getPost('title', $page ? $page->title : null);
        $content = $this->request->getPost('content', $page ? $page->content : null);
        $errorMessage = null;
        
        if ($this->request->isPost()) {
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
                $oldPages = $this->storage->read('pages')->entities;
                if ($checkUnique) {
                    $pages->entities = $oldPages;
                    $pages->entities[] = (object) array(
                        'id' => $url,
                        'url' => "/pages/$url",
                        'title' => $title,
                        'menu' => false,
                    );
                } else {
                    foreach ($oldPages as &$oldPage) {
                        if ($oldPage->id == $url) {
                            $oldPage = (object) array(
                                'id' => $url,
                                'url' => "/pages/$url",
                                'title' => $title,
                                'menu' => $oldPage->menu,
                            );
                        }
                        $pages->entities[] = $oldPage;
                    }
                }
                $this->storage->write($pages);
                return $this->redirect('/admin/pages');
            } else {
                $errorMessage = Translate::t($page->getValidationErrors(0));
            }
        }
        $this->data = array(
            'errorMessage' => $errorMessage,
            'url' => $url,
            'title' => $title,
            'content' => $content,
        );
    }

    public function pageseditAction() {
        $this->addJs(
            'ckeditor/ckeditor.js',
            'ckeditor/adapters/jquery.js'
        );
        $id = isset($this->request->params[2]) ? $this->request->params[2] : 0;
        if (!$this->storage->exists('page', $id)) {
            return $this->redirect('/admin/pages');
        }
        $model = new Page;
        $page = $this->storage->read('page', $id);
        foreach ($page as $key => $value) {
            $model->$key = $value;
        }
        return $this->pagesnewAction(false, $model);
    }

    public function usersDeleteAction() {
        $login = $this->request->getPost('id');
        $users = $this->storage->read('users');
        $model = new Users;
        foreach ($users->entities as $user) {
            if ($user->login != $login) {
                $model->entities[] = $user;
            }
        }
        $this->data['success'] = $this->storage->write($model);
        $this->renderJson = true;
    }

    public function usersnewAction($checkUnique = true, $login = null) {
        if (!$login) {
            $login = $this->request->getPost('login');
        }
        $password = $this->request->getPost('password');
        $confirm = $this->request->getPost('confirm');
        $errorMessage = null;

        if ($this->request->isPost()) {
            $users = $this->storage->read('users');
            if ($checkUnique && $this->userExists($users, $login)) {
                $errorMessage = Translate::t('User already exists');
            }
            $model = new Users;
            foreach ($users->entities as $user) {
                $model->entities[] = $user;
            }
            if (strlen($password) < 4) {
                $errorMessage = Translate::t('Password length must be at least 4 characters');
            }
            if ($password != $confirm) {
                $errorMessage = Translate::t('Passwords do not match');
            }
            if (!$errorMessage) {
                if ($checkUnique) {
                    $model->entities[] = (object) array(
                        'login' => $login,
                        'password' => $this->hash($password),
                    );
                } else {
                    foreach ($model->entities as &$user) {
                        if ($user->login == $login) {
                            $user->password = $this->hash($password);
                            break;
                        }
                    }
                }
                $this->storage->write($model);
                return $this->redirect('/admin/users');
            }
        }
        $this->data = array(
            'errorMessage' => $errorMessage,
            'login' => $login,
        );
    }

    public function userseditAction() {
        $login = isset($this->request->params[2]) ? $this->request->params[2] : 0;
        $users = $this->storage->read('users');
        if (!$this->userExists($users, $login)) {
            return $this->redirect('/admin/users');
        }
        return $this->usersnewAction(false, $login);
    }

    protected function userExists($users, $login) {
        $exists = false;
        foreach ($users->entities as $user) {
            if ($user->login == $login) {
                $exists = true;
                break;
            }
        }
        return $exists;
    }
}