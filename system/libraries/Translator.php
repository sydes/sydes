<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App;

class Translator
{

    public $installedPackages;
    private $container;
    private $locale = 'en';

    public function __construct()
    {
        $langs = glob(DIR_LANGUAGE.'/*');
        foreach ($langs as &$lang) {
            $lang = str_replace(DIR_LANGUAGE.'/', '', $lang);
        }
        $this->installedPackages = $langs;
    }

    public function loadPackage($locale = null)
    {
        $locale = $locale ?: $this->locale;
        if (!isset($this->container[$locale])) {
            $this->container[$locale] = include DIR_LANGUAGE.'/'.$locale.'/translation.php';
        }
        return $this;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    public function translate($text)
    {
        return isset($this->container[$this->locale][$text]) ?
            $this->container[$this->locale][$text] :
            (isset($this->container['en'][$text]) ?
                $this->container['en'][$text] :
                $text);
    }

    public function loadFrom($type, $name)
    {
        $base = $type == 'theme' ? DIR_THEME.'/'.$name : findExt($type, $name);
        $path = $base.'/languages/'.$this->locale.'.php';
        if (!file_exists($path)) {
            $path = $base.'/languages/en.php';
            if (!file_exists($path)) {
                return;
            }
        }
        $arr = include $path;
        $this->container[$this->locale] = isset($this->container[$this->locale]) ?
            array_merge($this->container[$this->locale], $arr) :
            $arr;
    }

}
