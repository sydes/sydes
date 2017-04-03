<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

use App\Settings\JsonDriver;

class Theme
{
    private $name;
    private $config;
    private $themeDir;
    private $layoutDir;
    private $configFile;
    private $ext = 'html';

    public function __construct($theme)
    {
        $this->name = $theme;
        $this->themeDir = DIR_THEME.'/'.$theme.'/';
        $this->layoutDir = $this->themeDir.'layouts/';
        $this->configFile = $this->themeDir.'theme.json';
    }

    /**
     * @param string $id alphanumeric name of layout
     * @param array $data array with keys content, extends, as
     * @return bool|int
     */
    public function storeLayout($id, array $data)
    {
        $content = arrayRemove($data, 'content', '');
        $formatted = '';
        foreach ($data as $key => $value) {
            $formatted .= "@{$key}({$value})";
        }
        $formatted .= "\n".$content;

        return file_put_contents($this->layoutDir.$id.'.'.$this->ext, $formatted);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getLayout($id)
    {
        if (!$this->hasLayout($id)) {
            throw new \RuntimeException(t('error_layout_not_found', ['id' => $id]));
        }

        $content = file_get_contents($this->layoutDir.$id.'.'.$this->ext);
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

    /**
     * @param string $id
     * @return bool
     */
    public function hasLayout($id)
    {
        return file_exists($this->layoutDir.$id.'.'.$this->ext);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function deleteLayout($id)
    {
        if (!$this->hasLayout($id)) {
            throw new \RuntimeException(t('error_layout_not_found', ['id' => $id]));
        }

        return unlink($this->layoutDir.$id.'.'.$this->ext);
    }

    /**
     * @param array $data
     * @return array
     */
    public function extendLayout($data)
    {
        $i = 0;
        while (isset($data['extends']) && $i++ != 10) {
            $data = $this->doExtend($data);
        }

        return $data;
    }

    private function doExtend($data)
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

    private function getFile($file)
    {
        return file_get_contents($this->themeDir.$file.'.'.$this->ext);
    }

    /**
     * @return Settings\Container
     */
    public function getConfig()
    {
        if (empty($this->config) && is_file($this->configFile)) {
            $this->config = new Settings\Container($this->configFile, new JsonDriver());
        }

        return $this->config;
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
        file_put_contents($dir.'theme.json', json_encode(['info' => [
            'name' => $name,
            'version' => '1.0',
        ]], JSON_PRETTY_PRINT));
        file_put_contents($dir.'layouts/page.'.$this->ext, "@extends(page) @as(Page)\n\n{content}\n");
        file_put_contents($dir.'page.'.$this->ext, '{layout}');
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
        file_put_contents($dir.'theme.json', json_encode(['info' => [
            'name' => $name,
            'version' => '1.0',
            'parent' => $this->name,
        ]], JSON_PRETTY_PRINT));
        file_put_contents($dir.'custom.css', '');
    }

    private function issetTheme($name)
    {
        return file_exists(DIR_THEME.'/'.$name);
    }

    /**
     * @return bool|null
     */
    public function delete()
    {
        return removeDir($this->themeDir);
    }
}
