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

    protected function getUploadFilePath($fieldName, $type) {
        return $this->config['files_dir'] . '/' . $type . '/' . $_FILES[$fieldName]['name'];
    }

    /**
     * @throws UploadException
     * @param string $fieldName
     */
    protected function validateUploadedFile($fieldName, $type) {
        $nameParts = explode('.', $_FILES[$fieldName]['name']);
        $extension = strtolower(array_pop($nameParts));
        if (!in_array($extension, $this->config['upload'][$type]['allowed_extensions'])) {
            unlink($_FILES[$fieldName]['tmp_name']);
            throw new UploadException('upload_error_extension', implode(', ', $this->config['upload'][$type]['allowed_extensions']));
        }
        if (filesize($_FILES[$fieldName]['tmp_name']) > $this->config['upload'][$type]['max_size']) {
            unlink($_FILES[$fieldName]['tmp_name']);
            throw new UploadException('upload_error_size', round($this->config['upload'][$type]['max_size'] / (1024 * 1024)));
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

    /**
     * @param string $appendix
     * @return \FilesystemIterator
     */
    protected function getIterator($appendix = '') {
        $paths = array(
            $this->config['files_dir'],
            $appendix,
        );
        return new \FilesystemIterator(implode(DIRECTORY_SEPARATOR, $paths));
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

    public function uploadFile($fieldName, $type) {
        $this->validateUploadedFile($fieldName, $type);
        $newPath = $this->getUploadFilePath($fieldName, $type);
        move_uploaded_file($_FILES[$fieldName]['tmp_name'], $newPath);
        return "/$newPath";
    }

    public function getUploadList($type) {
        $entities = array();
        foreach ($this->getIterator($type) as $path) {
            if ($path->isFile()) {
                $entities[] = array(
                    'name' => $path->getFilename(),
                    'url' => '/' . $this->config['files_dir'] . '/' . $type . '/' . $path->getFilename(),
                );
            }
        }
        return $entities;
    }

    public function getUploadUrl($url) {
        return $url;
    }

}