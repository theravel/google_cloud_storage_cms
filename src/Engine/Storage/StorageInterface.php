<?php

namespace Engine\Storage;

interface StorageInterface {
    public function exists($modelName);
    public function read($modelName);
    public function write($modelName);
    public function delete($modelName);
}