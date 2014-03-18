<?php

namespace Engine\Storage\Adapters;

use Engine\Storage\Models\BaseModel;

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

}