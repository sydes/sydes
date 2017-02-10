<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

class Api
{
    private $host = 'http://api.sydes.ru/';

    public function checkUpdate()
    {
        return $this->get('update/check/'.SYDES_VERSION);
    }

    public function getLocales()
    {
        return $this->json('locales');
    }

    public function loadLocale($locale)
    {
        return $this->get('locale/'.rawurlencode($locale));
    }

    public function getTranslations($module)
    {
        return $this->json('translations/'.rawurlencode($module));
    }

    public function loadTranslation($module, $locale)
    {
        return $this->get('translation/'.rawurlencode($module).'/'.rawurlencode($locale));
    }

    public function get($path)
    {
        $data = httpGet($this->host.$path.'?token='.md5($_SERVER['HTTP_HOST']));

        return !empty($data) ? $data : false;
    }

    public function json($path)
    {
        if (!$data = $this->get($path)) {
            return false;
        }

        return json_decode($data, true);
    }
}
