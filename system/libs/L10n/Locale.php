<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\L10n;

class Locale
{
    public function getEnglishName() {
        return $this->englishName;
    }

    public function getNativeName() {
        return $this->nativeName;
    }

    public function isRtl() {
        return $this->isRtl;
    }

    public function getIsoCode()
    {
        return $this->isoCode;
    }

    public function getPluralsCount()
    {
        return $this->pluralsCount;
    }

    public function date($format, $timestamp = null)
    {
        return is_null($timestamp) ? date($format) : date($format, $timestamp);
    }
}
