<?php

namespace App\Library\Storage\Models;

abstract class ListModel extends BaseModel {

    public $entities = array();

    public function getType() {
        return self::TYPE_LIST;
    }

}