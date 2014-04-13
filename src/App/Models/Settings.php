<?php

namespace App\Models;

use App\Library\Storage\Models\ListModel;

class Settings extends ListModel {

    public $title;

    public function getName() {
        return 'settings';
    }

}