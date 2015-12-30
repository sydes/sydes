<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App\Exception;

class ExceptionHandler {

    public function report(\Exception $e) {
        // TODO fire event
        return $this;
    }

    public function render(\Exception $e) {
        return $e instanceof \ErrorException ? $this->renderError($e) : $this->renderException($e);
    }

    private function renderError(\Exception $e) {
        $response = response();
        if (app('config')['app']['debug']) {
            $response->withContent(document()->alert('Err... this is error: '.
                $e->getMessage().'<br>'.pre($e->getTraceAsString(), true), 'danger'
            ));
        } else {
            $response->withContent(render(DIR_SYSTEM.'/templates/error_500.php', [$e]));
            $response->withStatus(500);
        }

        return $response;
    }

    private function renderException(\Exception $e) {
        $response = response(document()->alert($e->getMessage(), 'danger'));
        if ($e instanceof NotFoundHttpException) {
            $response->withStatus(404);
        } elseif ($e instanceof ForbiddenHttpException) {
            $response->withStatus(403);
        }
        return $response;
    }

}
