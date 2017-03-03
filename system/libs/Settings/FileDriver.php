<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Settings;

class FileDriver implements DriverInterface
{
    public function get($path)
    {
        $ret = [];
        if (file_exists($path)) {
            $ret = include $path;
        }

        return $ret;
    }

    public function set($path, $data)
    {
        array2file($data, $path);
    }
}
