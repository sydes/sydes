<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App;

class Translator {

    public $installedPackages;
    private $container;
    private $locale;

    public function __construct() {
        $langs = glob(DIR_LANGUAGE.'/*');
        foreach ($langs as &$lang) {
            $lang = str_replace(DIR_LANGUAGE.'/', '', $lang);
        }
        $this->installedPackages = $langs;
    }

    public function loadPackage() {
        $this->container[$this->locale] = include DIR_LANGUAGE.'/'.$this->locale.'/translation.php';
    }
    public function setLocale($locale) {
        $this->locale = $locale;
    }

    public function translate($text) {
        return isset($this->container[$this->locale][$text]) ?
            $this->container[$this->locale][$text] :
            (isset($this->container['en_US'][$text]) ?
                $this->container['en_US'][$text] :
                $text);
    }

    public function loadFrom($type, $name) {
        $base = findExt($type, $name);
        $path = $base.'/languages/'.$this->locale.'.php';
        if (file_exists($path)) {
            $path = $base.'/languages/en_US.php';
            if (file_exists($path)) {
                return;
            }
        }
        $arr = include $path;
        $this->container[$this->locale] = array_merge($this->container[$this->locale], $arr);
    }

}
