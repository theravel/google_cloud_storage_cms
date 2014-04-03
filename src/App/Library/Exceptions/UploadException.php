<?php

namespace App\Library\Exceptions;

class UploadException extends \Exception {

    private $details;

    public function __construct($message, $details = null) {
        parent::__construct($message);
        $this->details = $details;
    }

    public function getDetails() {
        return $this->details;
    }
}