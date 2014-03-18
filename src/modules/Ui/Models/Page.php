<?php

namespace Ui\Models;

use Engine\Storage\Models\BaseModel;

class Page extends BaseModel {

    public $url;
    public $title;
    public $content;

    public function getName() {
        return 'page';
    }

    public function getType() {
        return self::TYPE_ENTITY;
    }

    public function getId() {
        return $this->url;
    }

    public function setId($id) {
        $this->url = $id;
    }

}