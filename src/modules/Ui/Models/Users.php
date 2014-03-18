<?php

namespace Ui\Models;

use Engine\Storage\Models\BaseModel;

class users extends BaseModel {

    public $users = array();

    public function getName() {
        return 'users';
    }

    public function getType() {
        return self::TYPE_LIST;
    }

    public function getId() {
        
    }

    public function setId($id) {
        
    }

}