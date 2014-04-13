<?php

namespace App\Controller;

use App\Library\Exceptions\UploadException;

use App\Models\Page;
use App\Models\Pages;
use App\Models\Users;

class AdminController extends BaseController {

    public $layout = 'layout/admin';

    protected function hash($string) {
        return hash('sha256', $string, false);
    }

    protected function getNews() {
        return array();
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
        }
    }

    protected function getNewMenu() {
        $result = array();
        $raw = json_decode($this->request->getPost('new_items'));
        foreach ($raw as $menu) {
            $children = array();
            foreach ($menu->children as $submenu) {
                $children[] = array(
                    'id' => $submenu->id,
                    'text' => $submenu->text,
                    'link' => $submenu->link,
                );
            }
            $result[] = array(
                'id' => $menu->id,
                'text' => $menu->text,
                'link' => $menu->link,
                'children' => $children,
            );
        }
        return $result;
    }

    public function indexAction() {
        $this->addJs('jstree.min.js');
        $this->addCss('jstree/style.min.css');
        $this->data['saved'] = false;
        if ($this->request->isPost()) {
            $model = new \App\Models\Settings();
            $model->entities = $this->getNewMenu();
            $model->title = $this->request->getPost('title');
            $this->storage->write($model);
            $this->data['saved'] = true;
        }
        $settings = $this->storage->read('settings');
        $pages = array();
        foreach ($this->storage->read('pages')->entities as $page) {
            if (!$page->news) {
                $pages[] = $page;
            }
        }
        $this->data['pages'] = $pages;
        $this->data['title'] = $settings->title;
        $this->data['menu'] = json_encode($settings->entities);
    }

    public function loginAction() {
        $this->layout = 'layout/layout';
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
        $oldUrl = $this->request->getPost('oldUrl', $page ? $page->url : null);
        $title = $this->request->getPost('title', $page ? $page->title : null);
        $short = $this->request->getPost('short', $page ? $page->short : null);
        $content = $this->request->getPost('content', $page ? $page->content : null);
        $news = $this->request->isPost() ? (bool)$this->request->getPost('news') : ($page ? $page->news : false);
        $errorMessage = null;

        if ($this->request->isPost()) {
            $page = new Page;
            $page->url = $url;
            $page->title = $title;
            $page->short = $short;
            $page->content = $content;
            $page->news = $news;
            if ($checkUnique) {
                $this->storage->validateUnique($page);
            }
            if ($page->validate()) {
                if ($oldUrl) {
                    $this->storage->delete('page', $oldUrl);
                }
                $this->storage->write($page);
                $pages = new Pages;
                $oldPages = $this->storage->read('pages')->entities;
                if ($checkUnique) {
                    $pages->entities = $oldPages;
                    $pages->entities[] = (object) array(
                        'id' => $url,
                        'url' => "/pages/$url",
                        'title' => $title,
                        'short' => $short,
                        'news' => $news,
                        'date' => time(),
                    );
                } else {
                    foreach ($oldPages as &$oldPage) {
                        if ($oldPage->id == $oldUrl) {
                            $oldPage = (object) array(
                                'id' => $url,
                                'url' => "/pages/$url",
                                'title' => $title,
                                'short' => $short,
                                'news' => $news,
                                'date' => time()
                            );
                        }
                        $pages->entities[] = $oldPage;
                    }
                }
                $pages->sort();
                $this->storage->write($pages);
                return $this->redirect('/admin/pages');
            } else {
                $errorMessage = $this->t($page->getValidationErrors(0));
            }
        }
        $this->data = array(
            'errorMessage' => $errorMessage,
            'url' => $url,
            'oldUrl' => $oldUrl,
            'news' => $news,
            'title' => $title,
            'short' => $short,
            'content' => $content,
            'uploadFileUrl' => $this->storage->getUploadUrl('/admin/uploadfile'),
            'uploadImageUrl' => $this->storage->getUploadUrl('/admin/uploadimage'),
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
                $errorMessage = $this->t('validation_user_exists');
            }
            $model = new Users;
            foreach ($users->entities as $user) {
                $model->entities[] = $user;
            }
            if (strlen($password) < 4) {
                $errorMessage = $this->t('validation_pass_length');
            }
            if ($password != $confirm) {
                $errorMessage = $this->t('validation_pass_do_not_match');
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

    public function servefilesAction() {
        $this->layout = 'layout/files';
        $this->addJs('files.js');
        $this->data['files'] = $this->storage->getUploadList('files');
    }

    public function serveimagesAction() {
        $this->layout = 'layout/files';
        $this->addJs('files.js');
        $this->data['files'] = $this->storage->getUploadList('images');
    }

    public function uploadfileAction($type = 'files') {
        $this->layout = false;
        $this->data = array(
            'link' => null,
            'error' => null,
        );
        try {
            $this->data['link'] = $this->storage->uploadFile('upload', $type);
        } catch (UploadException $ex) {
            $this->data['error'] = sprintf(
                $this->t($ex->getMessage()),
                $ex->getDetails()
            );
        }
        $this->render('admin/upload');
    }

    public function uploadimageAction() {
        $this->uploadfileAction('images');
    }
}