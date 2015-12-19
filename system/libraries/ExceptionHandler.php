<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class ExceptionHandler {

    public static function render(Exception $e) {
        $response = new Response;

        if (app('config')['app']['debug']) {
            if ($e instanceof BaseException) {
                $response->body = $e->getMessage().' '.$e->status;
                $response->alert($e->getMessage(), $e->status);
                if (!is_null($e->redirect)) {
                    $response->redirect($e->redirect);
                }
            } else {
                $response->body = 'Err, this is error: '.$e->getMessage();
            }
        } else {
            $response->body = 'Something went wrong';
        }

        $response->body = render(DIR_SYSTEM.'/templates/error_500.php', [$e]);
        $response->status = 500;
        // TODO set page template

        return $response;
    }

}
