<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Main\Models;

use Psr\Container\ContainerInterface;
use Sydes\L10n\Locale;

class Translations
{
    /** @var \Sydes\Api */
    private $api;

    private $dirL10n;

    public function __construct(ContainerInterface $c)
    {
        $this->dirL10n = $c->get('dir.l10n');
        $this->api = $c->get('api');
    }

    /**
     * @param string $module
     * @return array
     */
    public function getInstalled($module)
    {
        $installed = str_replace([$this->dirL10n.'/', '/modules/'.$module.'.php'],
            '',
            glob($this->dirL10n.'/*/modules/'.$module.'.php')
        );

        return $this->format($installed);
    }

    /**
     * @param string $module
     * @param string $locale
     * @return bool
     */
    public function installed($module, $locale)
    {
        if ($locale == 'en') {
            return true;
        }

        return file_exists($this->dirL10n.'/'.$locale.'/modules/'.$module.'.php');
    }

    /**
     * @param string $module
     * @return array
     */
    public function getAvailable($module = null)
    {
        $list = $this->api->getTranslations($module ?: 'Main');

        return $this->format($list);
    }

    /**
     * @param array $modules
     * @param string $locale iso code
     */
    public function download($modules, $locale)
    {
        foreach ($modules as $module) {
            $data = $this->api->loadTranslation($module, $locale);

            if ($data) {
                $file = $this->dirL10n.'/'.$locale.'/modules/'.$module.'.php';
                file_put_contents($file, $data);
            }
        }
    }

    /**
     * @param array $list
     * @return array
     */
    private function format($list)
    {
        $ret = [
            'en' => 'English'
        ];

        foreach ($list as $key) {
            $className = 'Sydes\L10n\Locales\\'.ucfirst($key).'Locale';
            /** @var Locale $class */
            $class = new $className;
            $ret[$class->getisoCode()] = $class->getNativeName();
        }

        return $ret;
    }
}
