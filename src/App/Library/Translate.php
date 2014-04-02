<?php

namespace App\Library;

class Translate {

    protected static $config = array();
    protected static $dictionary = array();

    public static function setConfig(array $config) {
        self::$config = $config;
        $language = $config['language'];
        self::$dictionary = include "config/locale/$language/dictionary.php";
    }

    public static function t($id) {
        return isset(self::$dictionary[$id]) ? self::$dictionary[$id] : $id;
    }
}