<?php

namespace Engine\Storage\Models;

abstract class BaseModel {

    const TYPE_LIST = 'LIST';
    const TYPE_ENTITY = 'ENTITY';
    
    abstract public function getType();
    abstract public function getName();
    
}