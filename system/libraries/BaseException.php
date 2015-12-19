<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class BaseException extends RuntimeException {

    public function __construct($message, $status = 'danger', $redirect = null) {
        $this->status = $status;
        $this->redirect = $redirect;

        parent::__construct($message);
    }

}
