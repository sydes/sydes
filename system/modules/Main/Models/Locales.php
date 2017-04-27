<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Main\Models;

class Locales
{
    /** @var \Sydes\Api */
    private $api;

    public function __construct()
    {
        $this->api = app('api');
    }

    /**
     * @param string $locale iso code
     */
    public function downloadLocale($locale)
    {
        $data = $this->api->loadLocale($locale);

        if ($data) {
            $file = DIR_L10N.'/locales/'.ucfirst($locale).'.php';
            file_put_contents($file, $data);
        }
    }

    /**
     * @param string $module
     * @param string $locale iso code
     */
    public function downloadTranslations($modules, $locale)
    {
        foreach ($modules as $module) {
            $data = $this->api->loadTranslation($module, $locale);

            if ($data) {
                $file = DIR_L10N.'/translations/'.$locale.'/modules/'.$module.'.php';
                file_put_contents($file, $data);
            }
        }
    }
}
