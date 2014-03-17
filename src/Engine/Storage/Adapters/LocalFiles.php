<?php

namespace Engine\Storage\Adapters;

use Engine\Storage\StorageInterface;

class LocalFiles implements StorageInterface {

    protected $prefix = '';
    protected $suffix = '.php';
    protected $pagesDir = 'data/pages/';

    protected function getFilePath($modelName) {
        return $this->prefix . $this->pagesDir . $modelName . $this->suffix;
    }
    public function delete($modelName) {
        
    }

    public function read($modelName) {
        return file_get_contents($this->getFilePath($modelName));
    }

    public function write($modelName) {
        
    }

    public function exists($modelName) {
        return file_exists($this->getFilePath($modelName));
    }
}