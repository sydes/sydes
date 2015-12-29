<?php

namespace App\Exception;

class NotFoundHttpException extends HttpException {

    public function __construct($message = 'Page not found', $code = 0, \Exception $previous = null) {
        parent::__construct(404, $message, $code, $previous);
    }

}
