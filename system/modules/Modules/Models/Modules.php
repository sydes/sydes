<?php

namespace Module\Modules\Models;

use App\Settings\Container;
use App\Settings\JsonDriver;

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

        app('site')->set('modules', $modules);

        \App\App::execute([$name.'@install', [app('adminMenu')]], true);

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

            app('site')->set('modules', $modules);

            \App\App::execute([$name.'@uninstall', [app('adminMenu')]], true);

            app('cache')->flush();
        }
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
            $ret[$module] = $this->getManifest($module);
        }

        return $ret;
    }

    /**
     * Returns names of modules
     *
     * @param string $type 'default', 'custom', 'installed' or 'uninstalled'
     * @return array
     */
    public function filter($type)
    {
        if (empty($this->list)) {
            $installed = array_keys(app('site')->get('modules'));
            $this->list['default'] = str_replace(DIR_SYSTEM.'/modules/', '', glob(DIR_SYSTEM.'/modules/*', GLOB_ONLYDIR));
            $this->list['custom'] = str_replace(DIR_APP.'/modules/', '', glob(DIR_APP.'/modules/*', GLOB_ONLYDIR));
            $this->list['uninstalled'] = array_diff($this->list['custom'], $installed);
            $this->list['installed'] = array_diff($installed, $this->list['default']);
        }

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
}
