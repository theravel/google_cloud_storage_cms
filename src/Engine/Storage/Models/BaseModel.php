<?php

namespace Engine\Storage\Models;

abstract class BaseModel implements ModelInterface {

    const TYPE_LIST = 'LIST';
    const TYPE_ENTITY = 'ENTITY';
    
    abstract public function getType();
    abstract public function getName();
    abstract public function setId($id);
    abstract public function getId();
}