<?php

namespace App\Exception;

class HttpException extends \RuntimeException {

    /**
     * @var integer HTTP status code, such as 403, 404, 500, etc.
     */
    public $statusCode;

    public function __construct($status, $message = null, $code = 0, \Exception $previous = null) {
        $this->statusCode = $status;
        parent::__construct($message, $code, $previous);
    }

}
