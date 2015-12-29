<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class Response {

    public function redirect($url = '') {
        if (!empty($this->alerts)) {
            $_SESSION['alerts'] = $this->alerts;
            unset($this->alerts);
        }

        if (app('request')->is_ajax) {
            $this->body['redirect'] = $url;
        } else {
            $host = $_SERVER['HTTP_HOST'].'/';
            $this->addHeader('Location', app('request')->scheme.'://'.$host.$url);
            $this->status = 301;
        }

        return $this;
    }

    public function reload() {
        $this->body['reload'] = 1;
    }

    public function send() {
        if (!empty($this->notify)) {
            if (app('request')->is_ajax) {
                $this->body['notify'] = $this->notify;
            } else {
                $this->addCookie('notify.message', $this->notify['message'], 3);
                $this->addCookie('notify.status', $this->notify['status'], 3);
            }
        }

    }

}
