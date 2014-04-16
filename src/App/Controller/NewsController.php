<?php

namespace App\Controller;

class NewsController extends BaseController {

    public function indexAction() {
        $news = array();
        foreach ($this->storage->read('pages')->entities as $page) {
            if ($page->news) {
                $news[] = $page;
            }
        }
        // slider should be empty
        $this->layoutData['news'] = array();
        $this->data['news'] = $news;
    }

}