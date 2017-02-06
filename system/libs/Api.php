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
        return json_decode($this->get('l10n/locales'), true);
    }

    public function getLocale($locale)
    {
        return $this->get('l10n/locale/'.$locale);
    }

    public function getTranslations($module)
    {
        return json_decode($this->get('translations/'.$module), true);
    }

    public function get($path)
    {
        return httpGet($this->host.rawurlencode($path).'?token='.md5($_SERVER['HTTP_HOST']));
    }
}
