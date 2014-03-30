<?php

namespace App\Controller;

class ErrorController extends BaseController {
    public function indexAction() {
        $this->notFoundAction();
    }
}