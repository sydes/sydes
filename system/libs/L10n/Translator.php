<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\L10n;

class Translator
{
    public  $installedPackages;
    private $container = [];
    private $locale;

    public function __construct($locale = 'en')
    {
        $this->locale = $locale;
        $this->installedPackages = str_replace(DIR_L10N.'/', '', glob(DIR_L10N.'/*'));
    }

    public function loadPackage($locale = null)
    {
        $locale = $locale ?: $this->locale;
        if (in_array($locale, $this->installedPackages) && !isset($this->container[$locale])) {
            $this->container[$locale] = include DIR_L10N.'/'.$locale.'/translation.php';
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
        if ($type == 'theme') {
            $base = DIR_THEME.'/'.$name;
        } elseif ($type == 'module') {
            $base = moduleDir($name);
        } else {
            $base = iblockDir($name);
        }

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

    public function pluralize($text, $count, $context = [])
    {
        // расширить функцией множки от текущей локали
        // выбрать нужную строку
        // подставить контекст как в логгере

        return interpolate($text, $context);
    }
}
