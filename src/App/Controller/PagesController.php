<?php

namespace App\Controller;

class PagesController extends BaseController {

    const DEFAULT_PAGE = 'index';

    public function __call($name, $args) {
        $name = isset($this->request->params[1]) ? $this->request->params[1] : null;
        if (empty($name)) {
            $name = self::DEFAULT_PAGE;
        }
        if (!$this->storage->exists('page', $name)) {
            return $this->notFoundAction();
        }
        $page = $this->storage->read('page', $name);
        $this->data['content'] = $page->content;
        $this->render('pages/index');
    }

}