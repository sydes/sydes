<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Themes\Models;


class Themes
{
    private $ext = '.html';

    /**
     * @return array
     */
    public function getAll()
    {
        $data = [];
        $themes = str_replace(DIR_THEME.'/', '', glob(DIR_THEME.'/*', GLOB_ONLYDIR));
        foreach ($themes as $id) {
            $data[$id] = $this->get($id)->getInfo();
        }

        return $data;
    }

    /**
     * @param string $id
     * @return Theme
     */
    public function get($id)
    {
        return new Theme($id);
    }

    public function getActive()
    {
        $id = app('site')->get('theme');

        return $this->get($id);
    }

    /**
     * @param string $id
     */
    public function activate($id)
    {
        app('site')->set('theme', $id)->save();
    }

    /**
     * @param string $name
     * @param string $parent
     */
    public function create($name, $parent = null)
    {
        $id = snake_case($name, '-');

        if (is_dir(DIR_THEME.'/'.$id)) {
            throw new \RuntimeException(t('error_theme_already_exists', ['id' => $id]));
        }

        foreach ([$id, $id.'/layouts'] as $dir) {
            mkdir(DIR_THEME.'/'.$dir);
        }

        $dir = DIR_THEME.'/'.$id.'/';

        $manifest = ['info' => [
            'name' => $name,
            'version' => '1.0',
        ]];
        if ($parent) {
            $manifest['info']['parent'] = $parent;
        }

        $files = [
            'theme.json' => json_encode($manifest, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),
            'custom.css' => '',
        ];
        if (!$parent) {
            $files['layouts/page'.$this->ext] = "@extends(page) @name(Page)\n\n{content}\n";
            $files['page'.$this->ext] = "{layout}\n";
            $files['custom.js'] = '';
        }

        foreach ($files as $file => $data) {
            file_put_contents($dir.$file, $data);
        }
    }

    /**
     * @param string $id
     * @return bool|null
     */
    public function delete($id)
    {
        return removeDir(DIR_THEME.'/'.$id);
    }
}
