<?php

namespace App\Exception;

class BaseException extends RuntimeException {

    public $status;
    public $redirect;

    public function __construct($message, $status = 'danger', $redirect = null) {
        $this->status = $status;
        $this->redirect = $redirect;

        parent::__construct($message);
    }

}
