<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App\Exception;

class ExceptionHandler
{

    public function report(\Exception $e)
    {
        app('event')->trigger('exception', [$e], str_replace('App\Exception\\', '', get_class($e)));
        return $this;
    }

    public function render(\Exception $e)
    {
        return $e instanceof \ErrorException ? $this->renderError($e) : $this->renderException($e);
    }

    private function renderError(\Exception $e)
    {
        $response = response();
        if (app('config')['app']['debug']) {
            alert('Err... this is error: '.$e->getMessage().'<br>'.pre($e->getTraceAsString(), true), 'danger');
            $response->withContent(document());
        } else {
            $response->withContent(render(DIR_SYSTEM.'/views/error_500.php'));
            $response->withStatus(500);
        }

        return $response;
    }

    private function renderException(\Exception $e)
    {
        if ($e instanceof RedirectException) {
            return response()->withRedirect($e->url, $e->statusCode);
        }

        alert($e->getMessage(), 'danger');
        $response = response(document());
        if ($e instanceof ForbiddenHttpException) {
            $response->withStatus(403);
        } elseif ($e instanceof NotFoundHttpException) {
            $response->withStatus(404);
        }
        return $response;
    }

}
