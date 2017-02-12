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
    private $config = [];
    private $themeDir;
    private $layoutDir;
    private $configFile;

    public function __construct($theme)
    {
        $this->theme = $theme;
        $this->themeDir = DIR_THEME.'/'.$theme.'/';
        $this->layoutDir = $this->themeDir.'layouts/';
        $this->configFile = $this->themeDir.'config.json';
    }

    public function makeLayout($id, $source)
    {
        return true;
    }

    public function getLayout($id)
    {
        if (!$this->hasLayout($id)) {
            throw new \RuntimeException(sprintf(t('error_layout_not_found'), $id));
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
        $content = file_get_contents($this->layoutDir.$id.'.html');
        $firstLine = strtok($content, "\n");
        $data = [];

        if (preg_match_all('!@([a-z]+)\(([\w -/]+)\)!', $firstLine, $matches)) {
            foreach ($matches[1] as $i => $key) {
                $data[$key] = $matches[2][$i];
            }
        }

        $data['content'] = str_replace($firstLine, '', $content);

        return $data;
    }

    public function extendLayout($data)
    {
        if (strpos($data['extends'], '/') !== false) {
            $part = explode('/', $data['extends']);
            $parent = $this->getLayout($part[1]);
        } else {
            $parent = ['content' => $this->getFile($data['extends'])];
        }

        $parent['content'] = str_replace('{layout}', $data['content'], $parent['content']);

        return $parent;
    }

    public function getFile($file)
    {
        return file_get_contents($this->themeDir.$file.'.html');
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
        if (empty($this->config) && is_file($this->configFile)) {
            $this->config = parse_json_file($this->configFile);
        }

        return $this->config;
    }

    public function saveConfig()
    {
        return true;
    }
}
