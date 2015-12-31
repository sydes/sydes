<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App;

class Loader {

    public function model($module) {
        $part = strpos($module, '/') !== false ? explode('/', $module) : [$module, $module];
        $file = findExt('module', $part[0]).'/model/'.$part[1].'.php';

        if (!file_exists($file)) {
            throw new \RuntimeException(sprintf(t('error_file_not_found'), $file));
        }

        include_once $file;
        $class = ucfirst($part[1]).'Model';

        return new $class();
    }

    public function view($template, $data = []) {
        $part = explode('/', $template);
        if (count($part) != 2) {
            throw new \RuntimeException(t('error_loadview_argument'));
        }

        // TODO event before.render.partial with &$template & &$data

        $file_override = DIR_THEME.'/'.App::config()->site['theme'].'/module/'.$template.'.php';
        $file = findExt('module', $part[0]).'/view/'.$part[1].'.php';
        if (file_exists($file_override)) {
            $html = render($file_override, $data);
        } elseif (file_exists($file)) {
            $html = render($file, $data);
        } else {
            throw new \RuntimeException(sprintf(t('error_file_not_found'), $file));
        }

        // TODO event after.render.partial with &$html

        return $html;
    }

    public function config($model) {
        return new Config($model, app('db'));
    }

}
