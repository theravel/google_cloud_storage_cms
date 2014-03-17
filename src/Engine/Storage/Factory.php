<?php

namespace Engine\Storage;

class Factory {

    const TYPE_LOCAL = 'LocalFiles';
    const TYPE_GCS = 'GoogleCloudStorage';

    private static $adapters = array();

    /**
     * 
     * @param type $type
     * @return \Engine\Storage\StorageInterface
     */
    public static function getAdapter($type) {
        $validTypes = array(self::TYPE_GCS, self::TYPE_LOCAL);
        if (!in_array($type, $validTypes)) {
            $type = self::TYPE_LOCAL;
        }
        if (!isset(self::$adapters[$type])) {            
            $name = '\Engine\Storage\Adapters\\' . $type;
            self::$adapters[$type] = new $name;
        }
        return self::$adapters[$type];
    }

    public static function getCurrentAdapter() {
        if (isset($_SERVER['APPLICATION_ID'])) {
            return self::getAdapter(self::TYPE_GCS);
        }
        return self::getAdapter(self::TYPE_LOCAL);
    }
}