<?php

namespace Module\Modules\Models;

use Sydes\Settings\Container;
use Sydes\Settings\JsonDriver;
use Psr\Http\Message\UploadedFileInterface;

class Modules
{
    private $list = [];

    /**
     * @param string|array $modules
     */
    public function install($modules)
    {
        foreach ((array)$modules as $module) {
            $mods = $this->withRequired($module);
            $mods = array_reverse($mods);

            foreach ($mods as $module => $void) {
                $this->register($module);
            }
        }
    }

    /**
     * @param string $name
     */
    public function register($name)
    {
        $modules = app('site')->get('modules');
        $name = studly_case($name);
        if (isset($modules[$name])) {
            return;
        }

        $data = [];
        $dir = moduleDir($name);

        $iblocks = str_replace($dir.'/iblocks/', '', glob($dir.'/iblocks/*'));
        if (!empty($iblocks)) {
            $data['iblocks'] = $iblocks;
        }

        $files = str_replace($dir.'/functions/', '', glob($dir.'/functions/*'));
        if (!empty($files)) {
            $data['files'] = $files;
        }

        if (file_exists($dir.'/Handlers.php')) {
            $data['handlers'] = true;
        }

        if (file_exists($dir.'/Cli.php')) {
            $data['console'] = true;
        }

        $modules[$name] = $data;

        app('site')->set('modules', $modules)->save();

        $this->run($name, 'install');

        app('cache')->flush();
    }

    /**
     * @param string $name
     */
    public function uninstall($name)
    {
        $modules = app('site')->get('modules');
        $name = studly_case($name);
        if (isset($modules[$name])) {
            unset($modules[$name]);

            app('site')->set('modules', $modules)->save();

            $this->run($name, 'uninstall');

            app('cache')->flush();
        }
    }

    private function run($name, $method)
    {
        $class = 'Module\\'.$name.'\Controller';
        if (!class_exists($class)) {
            return;
        }

        $instance = new $class;
        if (!method_exists($instance, $method)) {
            return;
        }

        call_user_func_array([$instance, $method], [app('adminMenu')]);
    }

    /**
     * Returns manifests for modules
     *
     * @param string $type 'default', 'custom', 'installed' or 'uninstalled'
     * @return array
     */
    public function getList($type)
    {
        $ret = [];
        $modules = $this->filter($type);
        foreach ($modules as $module) {
            $ret[snake_case($module, '-')] = $this->getManifest($module);
        }

        return $ret;
    }

    public function getAll()
    {
        $this->load();

        return array_merge($this->list['default'], $this->list['custom']);
    }

    /**
     * Returns names of modules
     *
     * @param string $type 'default', 'custom', 'installed' or 'uninstalled'
     * @return array
     */
    public function filter($type)
    {
        $this->load();

        if (!array_key_exists($type, $this->list)) {
            throw new \OutOfBoundsException('$type should be default, custom, installed or uninstalled');
        }

        return $this->list[$type];
    }

    /**
     * Returns manifest for module
     *
     * @param string $name
     * @return Container
     */
    public function getManifest($name)
    {
        return new Container(moduleDir($name).'/module.json', new JsonDriver());
    }

    public function withRequired($module)
    {
        static $matrix = [];

        $matrix[$module] = true;

        $require = $this->getManifest($module)->get('require', []);
        foreach ($require as $mod) {
            $this->withRequired($mod);
        }

        return $matrix;
    }

    private function load()
    {
        if (empty($this->list)) {
            $installed = array_keys(app('site')->get('modules'));
            $this->list['default'] = str_replace(DIR_SYSTEM.'/modules/', '', glob(DIR_SYSTEM.'/modules/*', GLOB_ONLYDIR));
            $this->list['custom'] = str_replace(DIR_APP.'/modules/', '', glob(DIR_APP.'/modules/*', GLOB_ONLYDIR));
            $this->list['uninstalled'] = array_diff($this->list['custom'], $installed);
            $this->list['installed'] = array_diff($installed, $this->list['default']);
        }
    }

    public function uploadByUrl($url)
    {
        $temp = DIR_TEMP.'/'.token(5);

        if (extractOuterZip($temp, $url)) {
            $name = basename($url, '.zip');
            $name = $this->moveModule($name, $temp);
        }

        return $name;
    }

    public function uploadByFile(UploadedFileInterface $file)
    {
        if (pathinfo($file->getClientFilename(), PATHINFO_EXTENSION) != 'zip') {
            abort('403', t('only_zip_supported'));
        }

        $temp = DIR_TEMP.'/'.token(5);
        $file->moveTo($temp);

        $name = str_replace('.zip', '', $file->getClientFilename());
        $zip = new \ZipArchive;
        if ($zip->open($temp) === true) {
            $dir = DIR_TEMP.'/'.token(4);
            $zip->extractTo($dir);
            $zip->close();

            unlink($temp);

            $name = $this->moveModule($name, $dir);
        }

        return $name;
    }

    public function moveModule($name, $from)
    {
        $root = $from;
        if (!file_exists($from.'/module.json')) {
            $dirs = glob($from.'/*', GLOB_ONLYDIR);
            if (count($dirs) == 1 && file_exists($dirs[0].'/module.json')) {
                $from = $dirs[0];
                $name = basename($from);
            } else {
                removeDir($root);
                abort('404', t('not_found_module_in_archive'));
            }
        }

        $name = preg_replace('/(^sydes-|-module|-master$)/i', '', $name);

        rename($from, DIR_MODULE.'/'.studly_case($name));

        removeDir($root);

        return $name;
    }
}
