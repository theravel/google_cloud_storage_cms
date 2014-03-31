<?php

namespace App\Library\Storage\Adapters;

use App\Library\Storage\Models\BaseModel;
use App\Library\Storage\Models\EntityModel;

interface AdapterInterface {

    /**
     * @return bool
     */
    public function exists($modelName, $id = null);

    /**
     * @return BaseModel
     */
    public function read($modelName, $id = null);

    /**
     * @return bool
     */
    public function write(BaseModel $model);

    /**
     * @return bool
     */
    public function delete($modelName, $id = null);

    /**
     * @return bool
     */
    public function validateUnique(EntityModel &$model);

    /**
     * @return string
     */
    public function uploadFile($fieldName);
}