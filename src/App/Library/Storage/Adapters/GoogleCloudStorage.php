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
}