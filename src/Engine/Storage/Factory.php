<?php

namespace Engine\Storage;

class Factory {

    const TYPE_LOCAL = 'LocalFiles';
    const TYPE_GCS = 'GoogleCloudStorage';
    const TYPE_AUTO = 'auto';

    private static $adapters = array();

    /**
     * @param array $config
     * @return \Engine\Storage\AdapterInterface
     */
    public static function getAdapter(array $config) {
        $type = $config['type'];
        if (self::TYPE_AUTO == $type) {
            $type = Factory::TYPE_LOCAL;
            if (isset($_SERVER['APPLICATION_ID'])) {
                $type = Factory::TYPE_GCS;
            }
        }
        $validTypes = array(self::TYPE_GCS, self::TYPE_LOCAL);
        if (!in_array($type, $validTypes)) {
            $type = self::TYPE_LOCAL;
        }
        if (!isset(self::$adapters[$type])) {            
            $name = '\Engine\Storage\Adapters\\' . $type;
            self::$adapters[$type] = new $name($config);
        }
        return self::$adapters[$type];
    }

}