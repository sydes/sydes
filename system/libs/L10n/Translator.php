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
    protected $available;
    protected $container = [];
    protected $classes = [];
    protected $locale;

    /**
     * @param string $locale
     */
    public function init($locale)
    {
        $this->available = str_replace(DIR_L10N.'/translations/', '', glob(DIR_L10N.'/translations/*'));
        $this->available[] = 'en';

        $this->setLocale('en')->setLocale($locale);
    }

    /**
     * @param string $locale
     * @return $this
     * @throws \Exception
     */
    public function setLocale($locale)
    {
        if (!in_array($locale, $this->available)) {
            throw new \Exception('Locale "'.$locale.'" not available');
        }

        $this->locale = $locale;

        if (!isset($this->container[$locale])) {
            $this->container[$locale] = [];
        }

        if (!isset($this->classes[$locale])) {
            $class = 'Locales\\'.ucfirst($locale);
            $this->classes[$locale] = new $class;
        }

        $this->loadFrom('module', 'Main');

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->available;
    }

    /**
     * @param string $text
     * @param array  $context
     * @return string
     */
    public function translate($text, array $context = [])
    {
        $translated = isset($this->container[$this->locale][$text]) ?
            $this->container[$this->locale][$text] :
            (isset($this->container['en'][$text]) ?
                $this->container['en'][$text] :
                $text);

        return interpolate($translated, $context);
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

        if (!$base) {
            return;
        }

        $paths = [
            $base.'/languages/'.$this->locale.'.php',
            DIR_L10N.'/translations/'.$this->locale.'/'.$type.'s/'.$name.'.php',
            $base.'/languages/en.php',
        ];

        $inc = false;
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $inc = $path;
                break;
            }
        }

        if (!$inc) {
            return;
        }

        $arr = include $inc;
        $this->container[$this->locale] = array_merge($this->container[$this->locale], $arr);
    }

    public function pluralize($text, $count, $context = [])
    {
        if (!isset($this->container[$this->locale][$text]) ||
            !is_array($this->container[$this->locale][$text]) ||
            count($this->container[$this->locale][$text]) != $this->classes[$this->locale]->getPluralsCount()) {
            return $text;
        }

        $n = $this->classes[$this->locale]->plural($count);
        $msg = $this->container[$this->locale][$text][$n];

        return interpolate($msg, $context);
    }

    public function date($format, $timestamp = null)
    {
        return $this->classes[$this->locale]->date($format, $timestamp);
    }
}
