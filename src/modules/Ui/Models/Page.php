<?php

namespace Ui\Models;

use Engine\Storage\Models\EntityModel;

class Page extends EntityModel {

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

    public function validate() {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $this->url)) {
            $this->setValidationError('Url must contain only English letters, numbers, hyphen or underscore');
        }
        if (empty($this->title)) {
            $this->setValidationError('Title must be not empty');
        }
        if (empty($this->content)) {
            $this->setValidationError('Content must be not empty');
        }
        return empty($this->validationErrors);
    }

}