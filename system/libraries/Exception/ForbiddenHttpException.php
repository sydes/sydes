<?php

namespace App\Exception;

class ForbiddenHttpException extends HttpException
{

    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(403, $message ?: 'Access Forbidden', $code, $previous);
    }

}
