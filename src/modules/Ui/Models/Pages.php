<?php

namespace Ui\Models;

use Engine\Storage\Models\BaseModel;

class Pages extends BaseModel {

    public $pages = array();

    public function getName() {
        return 'pages';
    }

    public function getType() {
        return self::TYPE_LIST;
    }

    public function getId() {
        
    }

    public function setId($id) {
        
    }

}