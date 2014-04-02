<?php

namespace App\Models;

use App\Library\Storage\Models\EntityModel;

class Page extends EntityModel {

    public $id;
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
            $this->setValidationError('validation_url_rule');
        }
        if (empty($this->title)) {
            $this->setValidationError('validation_title_rule');
        }
        if (empty($this->content)) {
            $this->setValidationError('validation_content_rule');
        }
        return empty($this->validationErrors);
    }

}