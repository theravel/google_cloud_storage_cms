<?php

namespace App\Library\Storage\Adapters;

use App\Library\Storage\Models\BaseModel;

class GoogleCloudStorage extends LocalFiles {
    
    protected function getFilePath(BaseModel $model) {
        switch ($model->getType()) {
            case BaseModel::TYPE_ENTITY:
                return 'gs://' . $this->config['gcs_bucket_name'] . '/' . $this->config['data_dir'] . '/' . $model->getName() . '/' . $model->getId() . $this->config['suffix'];
            case BaseModel::TYPE_LIST:
                return 'gs://' . $this->config['gcs_bucket_name'] . '/' . $this->config['data_dir'] . '/' . $model->getName() . $this->config['suffix'];
        }
    }

    protected function getUploadFilePath($fieldName) {
        return 'gs://' . $this->config['files_dir'] . '/' . time() . '_' . $_FILES[$fieldName]['name'];
    }

    public function uploadFile($fieldName, $type) {
        $this->validateUploadedFile($fieldName, $type);
        $newPath = $this->getUploadFilePath($fieldName, $type);
        move_uploaded_file($_FILES[$fieldName]['tmp_name'], $newPath);
        return "/$newPath";
    }

    public function getUploadList($type) {  // files images      
        // https://developers.google.com/appengine/docs/php/googlestorage/public_access 
        // https://developers.google.com/appengine/docs/php/googlestorage/images
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
}