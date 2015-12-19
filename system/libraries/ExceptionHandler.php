<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class ExceptionHandler {

    public function report(Exception $e) {
        // TODO fire event
        return $this;
    }

    public function render(Exception $e) {
        $response = app('response');

        if ($e instanceof BaseException) {
            return $this->renderBusinessException($e, $response);
        } else {
            return $this->renderErrorException($e, $response);
        }
    }

    private function renderBusinessException($e, $response) {
        $response->body = $e->getMessage().' '.$e->status;
        $response->alert($e->getMessage(), $e->status);

        if (!is_null($e->redirect)) {
            $response->redirect($e->redirect);
        } else {
            // TODO render page template
        }

        return $response;
    }

    private function renderErrorException($e, $response) {
        if (app('config')['app']['debug']) {
            $response->body = 'Err... this is error: '.$e->getMessage().'<br>'.nl2br($e->getTraceAsString());
            // TODO change to alert and render page template
        } else {
            $response->body = render(DIR_SYSTEM.'/templates/error_500.php', [$e]);
            $response->status = 500;
        }

        return $response;
    }

}
