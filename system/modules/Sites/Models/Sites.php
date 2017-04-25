<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Sites\Models;

use Sydes\Settings\Container as Settings;
use Sydes\Settings\FileDriver;

class Sites
{
    /**
     * @return array
     */
    public function getAll()
    {
        $data = [];
        $sites = str_replace(DIR_SITE.'/', '', glob(DIR_SITE.'/*', GLOB_ONLYDIR));
        foreach ($sites as $id) {
            $data[$id] = $this->get($id);
        }

        return $data;
    }

    /**
     * @param int $id
     * @return Settings
     */
    public function get($id)
    {
        $path = DIR_SITE.'/'.$id.'/config.php';

        return new Settings($path, new FileDriver());
    }

    /**
     * @param array $params
     */
    public function create(array $params)
    {
        $sites = str_replace(DIR_SITE.'/', '', glob(DIR_SITE.'/*', GLOB_ONLYDIR));
        $id = empty($sites) ? 1 : max($sites) + 1;

        mkdir(DIR_SITE.'/'.$id);

        $params['modules'] = [];
        $params['menu'] = [];

        $this->save($id, $params);

        unset(app()['site']);
        app()['site'] = $this->get($id);
        app()['siteId'] = $id;

        $modules = model('Modules');
        $modules->install($modules->filter('default'));
    }

    /**
     * @param int   $id
     * @param array $params
     */
    public function save($id, array $params)
    {
        $this->get($id)->merge($params)->save();
    }

    /**
     * @param int $id
     * @return bool|null
     */
    public function delete($id)
    {
        return removeDir(DIR_SITE.'/'.$id);
    }

    /**
     * @param int $id
     */
    public function activate($id)
    {

    }

    /**
     * @param int $id
     */
    public function deactivate($id)
    {

    }
}
