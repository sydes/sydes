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
        $file = findPath('module', $part[0]).'/model/'.$part[1].'.php';

        if (!file_exists($file)) {
            throw new BaseException(sprintf(t('error_file_not_found'), $file));
        }

        include_once $file;
        $class = ucfirst($part[1]).'Model';

        return new $class();
    }

    public function view($template, $data = []) {
        $part = explode('/', $template);
        if (count($part) != 2) {
            throw new BaseException(t('error_loadview_argument'));
        }

        // TODO event before.render.partial with &$template & &$data

        $file_override = DIR_THEME.'/'.App::config()->site['theme'].'/module/'.$template.'.php';
        $file = findPath('module', $part[0]).'/view/'.$part[1].'.php';
        if (file_exists($file_override)) {
            $html = render($file_override, $data);
        } elseif (file_exists($file)) {
            $html = render($file, $data);
        } else {
            throw new BaseException(sprintf(t('error_file_not_found'), $file));
        }

        // TODO event after.render.partial with &$html

        return $html;
    }

    public function language($filename, $global = true, $language = false) {
        if (!$language) {
            //$language = App::env()->language;
        }

        /*
          $file = DIR_LANGUAGE . $language . '/' . $filename . '.php';
          if (!is_file($file)){
          $file = DIR_LANGUAGE . 'en/' . $filename . '.php';
          if (!is_file($file)){
          return;
          }
          }
          $array = include $file;
          if ($global){
          t('add', $array);
          } else {
          return $array;
          } */

        /*
          нужно загрузить языковые пакеты, переводы модулей, инфоблоков, фронта, еще чего нибудь.
          так же нужно загружать как глобально в App::language(), так и локально, для js localization

         */
    }

    public function config($model) {
        return new Config($model, App::db());
    }

}
