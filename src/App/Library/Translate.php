<?php

namespace App\Library;

class Translate {

    protected static $data = array();

    public static function setData(array $data) {
        self::$data = $data;
    }

    public static function t($id) {
        return isset(self::$data[$id]) ? self::$data[$id] : $id;
    }
}