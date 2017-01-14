<?php

class ThemeModel
{
    public function all()
    {
        $themes = str_replace(DIR_THEME.'/', '', glob(DIR_THEME.'/*', GLOB_ONLYDIR));
        $return = [];

        foreach ($themes as $themeName) {
            $theme = new \App\Theme($themeName);
            $config = $theme->getConfig();

            $screenshot = file_exists(DIR_THEME.'/'.$themeName.'/img/screenshot.jpg') ?
                '/themes/'.$themeName.'/img/screenshot.jpg' :
                '/system/modules/Theme/assets/img/no-image.jpg';

            $return[$themeName] = [
                'name' => $config['info']['name'],
                'authors' => ifsetor($config['info']['authors'], []),
                'screenshot' => $screenshot,
                'version' => ifsetor($config['info']['version'], '1.0'),
            ];
        }

        return $return;
    }
}
