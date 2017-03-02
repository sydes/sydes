<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

class SiteConfig
{
    private $config = [];
    private $id;

    public function __construct($id)
    {
        $path = DIR_SITE.'/'.$id.'/config.php';
        if (file_exists($path)) {
            $this->config = include $path;
        }
        $this->id = $id;
    }

    public function update($key, $value = null)
    {
        if (is_null($value) && is_array($key)) {
            $this->config = array_merge($this->config, $key);
        } else {
            $this->config[$key] = $value;
        }

        array2file($this->config, DIR_SITE.'/'.$this->id.'/config.php');
    }

    public function get($key)
    {
        return $this->config[$key];
    }
}
