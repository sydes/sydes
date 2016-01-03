<?php

namespace App\Exception;

class RedirectException extends HttpException {

    public $url;

    public function __construct($url, $code = 0, \Exception $previous = null) {
        $this->url = $url;
        parent::__construct(301, null, $code, $previous);
    }

}
