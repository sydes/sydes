<?php

class ThemeModel
{
    public function all()
    {
        $themes = str_replace(DIR_THEME.'/', '', glob(DIR_THEME.'/*', GLOB_ONLYDIR));
        $return = [];

        foreach ($themes as $name) {
            $theme = new App\Theme($name);
            $config = $theme->getConfig();

            $return[$name] = array_merge([
                'name' => 'Nameless Theme',
                'description' => '',
                'version' => '1.0',
                'authors' => [],
                'tags' => [],
            ], $config['info']);

            if (isset($config['info']['screenshot'])) {
                $return[$name]['screenshot'] = '/themes/'.$name.'/'.$config['info']['screenshot'];
            } else {
                $return[$name]['screenshot'] = assetsDir('theme').'/img/no-image.jpg';
            }
        }

        return $return;
    }

    public function activate($name)
    {
        app('site')->update('theme', $name);
    }
}
