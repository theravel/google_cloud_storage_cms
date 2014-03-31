<?php

namespace App\Library\Storage\Adapters;

use App\Library\Storage\Models\BaseModel;
use App\Library\Storage\Models\EntityModel;
use App\Library\Exceptions\UploadException;

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

    protected function getUploadFilePath($fieldName) {
        return $this->config['files_dir'] . '/' . time() . '_' . $_FILES[$fieldName]['name'];
    }

    /**
     * @throws UploadException
     * @param string $fieldName
     */
    protected function validateUploadedFile($fieldName) {
//        'extension' => array(
//              'rule' => array('extension'),
//              'message' => 'INVALID_EXTENSION',
//          ),
//          'fileSize' => array(
//              'rule' => array('fileSize'),
//              'message' => 'TOO_LARGE_FILE_SIZE',
//          ),
//          'mimeType' => array(
//              'rule' => array('mimeType'),
//              'message' => 'WRONG_MIME_TYPE',
//          ),
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

    public function validateUnique(EntityModel &$model) {
        if ($this->exists($model->getName(), $model->getId())) {
            $model->setValidationError('Url already exists');
            return false;
        }
        return true;
    }

    public function uploadFile($fieldName) {
        $this->validateUploadedFile($fieldName);
        $newPath = $this->getUploadFilePath($fieldName);
        move_uploaded_file($_FILES[$fieldName]['tmp_name'], $newPath);
        return "/$newPath";
    }

}