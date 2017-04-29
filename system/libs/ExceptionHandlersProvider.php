<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

use Pimple\ServiceProviderInterface;
use Sydes\Exception\AppException;
use Sydes\Exception\BaseHandler;
use Sydes\Exception\RedirectException;

class ExceptionHandlersProvider implements ServiceProviderInterface
{
    public function register(\Pimple\Container $c)
    {
        $c['RedirectExceptionHandler'] = $c->protect(function (RedirectException $e) {
            return redirect($e->getUrl());
        });

        $c['AppExceptionHandler'] = $c->protect(function (AppException $e) {
            $doc = document();
            if (app('section') == 'front') {
                if (model('Themes')->getActive()->getLayouts()->exists('error'.$e->getCode())) {
                    $doc->data['layout'] = 'error'.$e->getCode();
                } else {
                    $doc->data['content'] = '<h1>'.t('error_'.$e->getCode().'_text').'</h1><p>'.$e->getMessage().'</p>';
                }
            } else {
                alert($e->getMessage(), 'danger');
            }

            return html(app('renderer')->render($doc), $e->getCode());
        });

        $c['ConfirmationExceptionHandler'] = $c->protect(function () {
            $doc = document([
                'content' => view('main/confirm', [
                    'message'    => t('confirm_deletion'),
                    'return_url' => app('request')->getHeaderLine('Referer') ?: '/admin',
                ]),
            ]);

            return html(app('renderer')->render($doc), 200);
        });

        $c['defaultErrorHandler'] = $c->protect(function (\Exception $e) use ($c) {
            $debugLevel = $c['settings']['debugLevel'];
            $handler = new BaseHandler;

            if ($debugLevel == 0) {
                return $handler->defaultResponse();
            }

            alert($e->getMessage().'<br>'.$handler->getContent($e, $debugLevel), 'danger');

            return html(app('renderer')->render(document()), 500);
        });

        $c['finalExceptionHandler'] = function () {
            return new Exception\BaseHandler();
        };
    }
}
