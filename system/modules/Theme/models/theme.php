<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Theme\Models;

use App\Settings\Container as Settings;
use App\Settings\JsonDriver;

class Theme
{
    /** @var Layouts */
    private $layouts;
    private $name;
    private $config;
    private $themeDir;
    private $configFile;
    private $ext = 'html';

    public function __construct($name = false)
    {
        $this->name = $name ?: app('site')->get('theme');
        $this->layouts = new Layouts($this->name);
        $this->themeDir = DIR_THEME.'/'.$this->name.'/';
        $this->configFile = $this->themeDir.'theme.json';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Layouts
     */
    public function getLayouts()
    {
        return $this->layouts;
    }

    /**
     * @return Settings
     */
    public function getConfig()
    {
        if (empty($this->config) && is_file($this->configFile)) {
            $this->config = new Settings($this->configFile, new JsonDriver());
        }

        return $this->config;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $themes = str_replace(DIR_THEME.'/', '', glob(DIR_THEME.'/*', GLOB_ONLYDIR));
        $data = [];

        foreach ($themes as $name) {
            $config = new Settings(DIR_THEME.'/'.$name.'/theme.json', new JsonDriver());

            $data[$name] = array_merge([
                'name' => 'Nameless Theme',
                'description' => '',
                'version' => '1.0',
                'authors' => [],
                'tags' => [],
                'screenshot' => '',
            ], $config->get('info'));

            if (!empty($data[$name]['screenshot'])) {
                $data[$name]['screenshot'] = '/themes/'.$name.'/'.$data[$name]['screenshot'];
            } else {
                $data[$name]['screenshot'] = assetsDir('theme').'/img/no-image.jpg';
            }
        }

        return $data;
    }

    /**
     * @param string $name
     */
    public function activate($name)
    {
        app('site')->set('theme', $name);
    }

    /**
     * @param string $name theme name
     */
    public function makeNew($name)
    {
        $slug = snake_case($name, '-');

        if ($this->issetTheme($slug)) {
            throw new \RuntimeException(t('error_theme_already_exists', ['slug' => $slug]));
        }

        foreach ([$slug, $slug.'/layouts'] as $dir) {
            mkdir(DIR_THEME.'/'.$dir);
        }

        $dir = DIR_THEME.'/'.$slug.'/';
        file_put_contents($dir.'theme.json', json_encode([
            'info' => [
                'name' => $name,
                'version' => '1.0',
            ],
        ], JSON_PRETTY_PRINT));
        file_put_contents($dir.'layouts/page.'.$this->ext, "@extends(page) @as(Page)\n\n{content}\n");
        file_put_contents($dir.'page.'.$this->ext, "{layout}\n");
        file_put_contents($dir.'custom.css', '');
        file_put_contents($dir.'custom.js', '');
    }

    /**
     * @param string $name theme name
     */
    public function makeChild($name)
    {
        $slug = snake_case($name, '-');

        if ($this->issetTheme($slug)) {
            throw new \RuntimeException(t('error_theme_already_exists', ['slug' => $slug]));
        }

        foreach ([$slug, $slug.'/layouts'] as $dir) {
            mkdir(DIR_THEME.'/'.$dir);
        }

        $dir = DIR_THEME.'/'.$slug.'/';
        file_put_contents($dir.'theme.json', json_encode([
            'info' => [
                'name'    => $name,
                'version' => '1.0',
                'parent'  => $this->name,
            ],
        ], JSON_PRETTY_PRINT));
        file_put_contents($dir.'custom.css', '');
    }

    private function issetTheme($name)
    {
        return is_dir(DIR_THEME.'/'.$name);
    }

    /**
     * @param string $name
     * @return bool|null
     */
    public function delete($name = false)
    {
        return $name ? removeDir(DIR_THEME.'/'.$name) : removeDir($this->themeDir);
    }
}
