<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Themes\Models;

use App\Settings\Container as Settings;
use App\Settings\JsonDriver;

class Theme
{
    private $id;
    private $dir;
    private $config;
    private $layouts;
    private $parentTheme;

    public function __construct($id)
    {
        $this->id = $id;
        $this->dir = DIR_THEME.'/'.$id;

        if (!file_exists($this->dir.'/theme.json')) {
            throw new \Exception(t('error_theme_manifest_not_found', ['id' => $id]));
        }

        $parent = $this->getInfo('parent');
        if ($parent) {
            if (!is_dir(DIR_THEME.'/'.$parent)) {
                throw new \Exception(t('error_parent_theme_not_found', ['id' => $parent]));
            }

            $this->parentTheme = new self($parent);
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Layouts
     */
    public function getLayouts()
    {
        if (is_null($this->layouts)) {
            $parent = $this->parentTheme ? $this->parentTheme->getLayouts() : null;
            $this->layouts = new Layouts($this->id, $parent);
        }

        return $this->layouts;
    }

    /**
     * @return Settings
     */
    public function getConfig()
    {
        if (is_null($this->config)) {
            $this->config = new Settings($this->dir.'/theme.json', new JsonDriver());
        }

        return $this->config;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getInfo($key = null)
    {
        $data = array_merge([
            'name' => 'Nameless Theme',
            'description' => '',
            'version' => '1.0',
            'authors' => [],
            'tags' => [],
            'screenshot' => '',
        ], $this->getConfig()->get('info'));

        if (empty($data['screenshot'])) {
            $data['screenshot'] = assetsDir('themes').'/img/no-image.jpg';
        } else {
            $data['screenshot'] = '/themes/'.$this->id.'/'.$data['screenshot'];
        }

        return $key ? ifsetor($data[$key]) : $data;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getSettings($key = null)
    {
        $data = $this->getConfig()->get('settings', []);

        if ($this->parentTheme) {
            $data = array_merge_recursive($this->parentTheme->getSettings(), $data);
        }

        return $key ? ifsetor($data[$key]) : $data;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getData($key = null)
    {
        $data = $this->getConfig()->get('data', []);

        if ($this->parentTheme) {
            $data = array_merge_recursive($this->parentTheme->getData(), $data);
        }

        return $key ? ifsetor($data[$key]) : $data;
    }

    /**
     * @param string $code js or css
     * @return array
     */
    public function getAssets($code)
    {
        $data = $this->getConfig()->get($code, []);
        foreach ($data as $key => $source) {
            $data[$key] = $this->prependPath($source);
        }

        if ($this->parentTheme) {
            $data = array_merge($this->parentTheme->getAssets($code), $data);

            foreach ((array)$this->getConfig()->get('hide_'.$code, []) as $item) {
                unset($data[$item]);
            }
        }

        if (is_file($this->dir.'/custom.'.$code)) {
            $data['custom'] = '/themes/'.$this->id.'/custom.'.$code;
        }

        return $data;
    }

    private function prependPath($source)
    {
        $arr = [];
        foreach ((array)$source as $path) {
            $arr[] = ($path[0] != '/' && substr($path, 0, 4) != 'http') ? '/themes/'.$this->id.'/'.$path : $path;
        }

        return $arr;
    }

    public function getIblockDir($iblock)
    {
        $dir = $this->dir.'/iblocks/'.$iblock;

        if (file_exists($dir.'/iblock.php')) {
            return $dir;
        } elseif ($this->parentTheme) {
            return $this->parentTheme->getIblockDir($iblock);
        }

        return false;
    }

    public function getThemedView($entity, $name, $view)
    {
        $places = [
            'iblock' => $this->dir.'/iblocks/'.$name.'/views/'.$view.'.php',
            'module' => $this->dir.'/modules/'.$name.'/'.$view.'.php'
        ];
        $dir = $places[$entity];

        if (file_exists($dir)) {
            return $dir;
        } elseif ($this->parentTheme) {
            return $this->parentTheme->getThemedView($entity, $name, $view);
        }

        return false;
    }
}
