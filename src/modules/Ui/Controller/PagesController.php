<?php

namespace Ui\Controller;

use Zend\View\Model\ViewModel;

use Ui\Models\Pages;

class PagesController extends UiController {

    const DEFAULT_PAGE = 'home';

    public function pageAction() {
        $name = $this->getEvent()->getRouteMatch()->getParam('name');
        if (empty($name)) {
            $name = 'home';
        }
        if (!$this->storage->exists('page', $name)) {
            return $this->notFoundAction();
        }
        $page = $this->storage->read('page', $name);
        return new ViewModel(array(
            'content' => $page->content,
        ));
    }

}