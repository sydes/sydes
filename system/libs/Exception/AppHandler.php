<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Exception;

class AppHandler extends BaseHandler
{
    public function render(\Exception $e, $debugLevel)
    {
        if ($e instanceof RedirectException) {
            return redirect($e->getUrl());
        } elseif ($e instanceof AppException) {
            return $this->businessException($e);
        } elseif ($e instanceof ConfirmationException) {
            return $this->deleteConfirmation();
        }

        return $this->errorAlert($e, $debugLevel);
    }

    protected function businessException(\Exception $e)
    {
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
    }

    protected function deleteConfirmation()
    {
        $doc = document([
            'content' => view('main/confirm', [
                'message' => t('confirm_deletion'),
                'return_url' => app('request')->getHeaderLine('Referer') ?: '/admin',
            ])]);

        return html(app('renderer')->render($doc), 200);
    }

    protected function errorAlert(\Exception $e, $debugLevel)
    {
        if ($debugLevel == 0) {
            return $this->defaultResponse();
        }

        alert($e->getMessage().'<br>'.$this->getContent($e, $debugLevel), 'danger');

        return html(app('renderer')->render(document()), 500);
    }
}
