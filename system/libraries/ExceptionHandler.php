<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class ExceptionHandler {
    public static function render($request, Exception $e) {
        $response = new Response;

        if (DEBUG){
            $response->alert('Err, this is error: '.$e->getMessage());
        } else {
            $response->alert('Something went wrong');
        }

        // TODO set page template

        return $response;
    }
}
