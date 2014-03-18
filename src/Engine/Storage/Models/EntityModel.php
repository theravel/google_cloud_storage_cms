<?php

namespace Engine\Storage\Models;

abstract class EntityModel extends BaseModel {

    protected $validationErrors = array();

    public function getType() {
        return self::TYPE_ENTITY;
    }

    public function getValidationErrors($index = null) {
        if (null === $index) {
            return $this->validationErrors;
        } else {
            return $this->validationErrors[$index];
        }
    }

    public function setValidationError($error) {
        $this->validationErrors[] = $error;
    }

    abstract public function getId();
    abstract public function setId($id);
    abstract public function validate();

}