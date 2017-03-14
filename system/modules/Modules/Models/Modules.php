<?php

namespace Module\Modules\Models;

class Modules
{
    /**
     * @param string $name
     */
    public function install($name)
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
}
