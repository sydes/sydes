<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Exception;

class SiteHandler extends BaseHandler
{
    public function render(\Exception $e, $debug)
    {
        if ($e instanceof RedirectException) { // redirect

            return redirect($e->getUrl());

        } elseif ($e instanceof AppException) { // business exception

            $doc = document();
            if (app('section') == 'front') {
                if (app('theme')->hasLayout('error'.$e->getCode())) {
                    $doc->data['layout'] = 'error'.$e->getCode();
                } else {
                    $doc->data['content'] = t('error_'.$e->getCode().'_text');
                }
            } else {
                alert($e->getMessage(), 'danger');
            }

            return html(app('renderer')->render($doc), $e->getCode());

        } else { // error

            if ($debug == 0) {

                return $this->defaultResponse();

            } else {

                $text = $e->getMessage().'<br>'.$e->getFile().' on '.$e->getLine();

                if ($debug === 2) {
                    $text .= nl2br("\n\n".$e->getTraceAsString());
                }

                alert($text, 'danger');

                return html(app('renderer')->render(document()), 500);

            }
        }
    }
}
