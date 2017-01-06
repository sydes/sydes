<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

class Theme
{
    private $theme;
    private $layoutDir;
    private $config;
    private $configFile;

    public function __construct($theme)
    {
        $this->theme = $theme;
        $this->layoutDir = DIR_THEME.'/'.$theme.'/layouts/';
        $this->configFile = DIR_THEME.'/'.$theme.'/config.json';
    }

    public function makeLayout($id, $source)
    {
        return true;
    }

    public function getLayout($id)
    {
        if (!$this->hasLayout($id)) {
            return false;
        }
        return $this->parseLayout($id);
    }

    public function hasLayout($id)
    {
        return file_exists($this->layoutDir.$id.'.html');
    }

    public function storeLayout($id, $source)
    {
        return true;
    }

    public function deleteLayout($id)
    {
        return true;
    }

    private function parseLayout($id)
    {
        return [
            'id' => $id,
            'name' => '',
            'extends' => '',
        ];
    }

    public function make($name)
    {
        return true;
    }

    public function index()
    {
        return [];
    }

    public function getConfig()
    {
        if (!empty($this->config)) {
            return $this->config;
        } elseif (is_file($this->configFile)) {
            $this->config = parse_json_file($this->configFile);
        }
        return $this->config;
    }

    public function saveConfig()
    {
        return true;
    }
}
