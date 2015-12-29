<?php

namespace App\Exception;

class ForbiddenHttpException extends HttpException {

    public function __construct($message = 'Access Forbidden', $code = 0, \Exception $previous = null) {
        parent::__construct(403, $message, $code, $previous);
    }

}
