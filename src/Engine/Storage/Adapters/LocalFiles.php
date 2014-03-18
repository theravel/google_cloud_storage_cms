<?php

namespace Engine\Storage\Adapters;

use Engine\Storage\Models\BaseModel;

class LocalFiles implements AdapterInterface {

    protected $config = array();

    protected function getFilePath(BaseModel $model) {
        switch ($model->getType()) {
            case BaseModel::TYPE_ENTITY:
                return $this->config['data_dir'] . '/' . $model->getName() . '/' . $model->getId() . $this->config['suffix'];
            case BaseModel::TYPE_LIST:
                return $this->config['data_dir'] . '/' . $model->getName() . $this->config['suffix'];
        }
    }

    /**
     * @param string $modelname
     * @return BaseModel
     */
    protected function getModel($modelName, $id = null) {
        $className = $this->config['models']['namespace'] . '\\' . ucfirst(strtolower($modelName));
        $model = new $className;        
        if (BaseModel::TYPE_ENTITY == $model->getType()) {
            $model->setId($id);
        }
        return $model;
    }

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function delete($modelName, $id = null) {
        $model = $this->getModel($modelName, $id);
        return unlink($this->getFilePath($model));
    }

    public function read($modelName, $id = null) {
        $model = $this->getModel($modelName, $id);
        $content = file_get_contents($this->getFilePath($model));
        return json_decode($content);
    }

    public function write(BaseModel $model) {
        $content = json_encode($model);
        return false !== file_put_contents($this->getFilePath($model), $content);
    }

    public function exists($modelName, $id = null) {
        $model = $this->getModel($modelName, $id);
        return file_exists($this->getFilePath($model));
    }
}