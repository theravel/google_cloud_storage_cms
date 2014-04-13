<?php

namespace App\Controller;

class NewsController extends BaseController {

    public function indexAction() {
        $this->layout = 'layout/news';
        $news = array();
        foreach ($this->storage->read('pages')->entities as $page) {
            if ($page->news) {
                $news[] = $page;
            }
        }
        $this->data['news'] = $news;
    }

}