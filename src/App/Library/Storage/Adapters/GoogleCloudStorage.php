<?php

namespace App\Library\Storage\Adapters;

include 'google/appengine/api/cloud_storage/CloudStorageTools.php';
use google\appengine\api\cloud_storage\CloudStorageTools;
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

    protected function getUploadFilePath($fieldName, $type) {
        return 'gs://' . $this->config['gcs_bucket_name'] . '/' . $this->config['files_dir'] . "/$type/" . $_FILES[$fieldName]['name'];
    }

    protected function getPublicUploadUrl($type, $fileName) {
        $path = 'gs://' . $this->config['gcs_bucket_name'] . '/' . $this->config['files_dir'] . "/$type/$fileName";
        return CloudStorageTools::getPublicUrl($path, false);
    }

    /**
     * @param string $appendix
     * @return \FilesystemIterator
     */
    protected function getIterator($appendix = '') {
        $paths = array(
            'gs:/',
            $this->config['gcs_bucket_name'],
            $this->config['files_dir'],
            $appendix,
        );
        return new \FilesystemIterator(implode(DIRECTORY_SEPARATOR, $paths));
    }

    public function getUploadUrl($url) {
        return CloudStorageTools::createUploadUrl($url, array(
            'gs_bucket_name' => $this->config['gcs_bucket_name'],
        ));
    }

    public function uploadFile($fieldName, $type) {
        $this->validateUploadedFile($fieldName, $type);
        $newPath = $this->getUploadFilePath($fieldName, $type);
        stream_context_set_default(array(
            'gs' => array(
                'acl' => 'public-read',
             ),
        ));
        move_uploaded_file($_FILES[$fieldName]['tmp_name'], $newPath);
        return $this->getPublicUploadUrl($type, $_FILES[$fieldName]['name']);
    }

    public function getUploadList($type) {
        $entities = array();
        foreach ($this->getIterator($type) as $path) {
            if ($path->isFile()) {
                $entities[] = array(
                    'name' => $path->getFilename(),
                    'url' => $this->getPublicUploadUrl($type, $path->getFilename()),
                );
            }
        }
        return $entities;
    }
}