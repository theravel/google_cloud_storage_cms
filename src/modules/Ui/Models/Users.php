<?php

namespace Ui\Models;

use Engine\Storage\Models\ListModel;

class Users extends ListModel {

    public function getName() {
        return 'users';
    }

}