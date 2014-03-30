<?php

namespace App\Models;

use App\Library\Storage\Models\ListModel;

class Users extends ListModel {

    public function getName() {
        return 'users';
    }

}