<?php

namespace Module\Modules\Models;

use Sydes\Exception\RedirectException;
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

        app('adminMenu')->save();
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
        if (!$dir = moduleDir($name)) {
            throw new \Exception(t('error_module_not_found', ['name' => $name]));
        }

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
            $this->checkDependencies($name);
            unset($modules[$name]);

            app('site')->set('modules', $modules)->save();

            $this->run($name, 'uninstall');

            app('cache')->flush();
        }
    }

    private function run($name, $method)
    {
        $class = 'Module\\'.$name.'\Runner';
        if (!class_exists($class)) {
            return;
        }

        $instance = new $class;
        if (!method_exists($instance, $method)) {
            return;
        }

        app()->call([$instance, $method]);
    }

    private function checkDependencies($name)
    {
        foreach ($this->getList('installed') as $module) {
            if (in_array($name, $module->get('require', []))) {
                alert(t('error_this_required_for_module', [
                    'this' => $this->getManifest($name)->get('name'),
                    'module' => $module->get('name'),
                ]), 'danger');

                throw new RedirectException('/admin/modules');
            }
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
            $installed = array_keys(app('site')->get('modules', []));
            $this->list['default'] = str_replace(app('dir.system').'/modules/', '', glob(app('dir.system').'/modules/*', GLOB_ONLYDIR));
            $this->list['custom'] = str_replace(app('dir.module').'/', '', glob(app('dir.module').'/*', GLOB_ONLYDIR));
            $this->list['uninstalled'] = array_diff($this->list['custom'], $installed);
            $this->list['installed'] = array_diff($installed, $this->list['default']);
        }
    }

    public function uploadByUrl($url)
    {
        $temp = app('dir.temp').'/'.token(5);

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

        $temp = app('dir.temp').'/'.token(5);
        $file->moveTo($temp);

        $name = str_replace('.zip', '', $file->getClientFilename());
        $zip = new \ZipArchive;
        if ($zip->open($temp) === true) {
            $dir = app('dir.temp').'/'.token(4);
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

        rename($from, app('dir.module').'/'.studly_case($name));

        removeDir($root);

        return $name;
    }
}
