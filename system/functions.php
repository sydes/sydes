<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

use Sydes\Exception\AppException;

/**
 * Find path to infoBlock in theme or app.
 *
 * @param string $name
 * @return false|string
 */
function iblockDir($name)
{
    if ($dir = model('Themes')->getActive()->getIblockDir($name)) {
        return $dir;
    }

    $dir = app('dir.app').'/iblocks/'.$name;
    if (file_exists($dir.'/iblock.php')) {
        return $dir;
    }

    foreach (app('site')->get('modules') as $modName => $module) {
        if (isset($module['iblocks']) && in_array($name, $module['iblocks'])) {
            return moduleDir($modName).'/iblocks/'.$name;
        }
    }

    return false;
}

/**
 * Find path to module in core or user folders.
 *
 * @param string $name
 * @return false|string
 */
function moduleDir($name)
{
    $name = studly_case($name);

    foreach ([app('dir.app'), app('dir.system')] as $place) {
        $path = $place.'/modules/'.$name;
        if (is_dir($path)) {
            return $path;
        }
    }

    return false;
}

/**
 * Find path to module's assets.
 *
 * @param $module
 * @return false|string
 */
function assetsPath($module)
{
    if ($dir = moduleDir($module)) {
        return str_replace(app('dir.root'), '', $dir).'/assets';
    }

    return false;
}

/**
 * @param int    $code
 * @param string $message
 * @throws AppException
 */
function abort($code, $message = '')
{
    throw new AppException($message, $code);
}

/**
 * Loads model of the specified module
 *
 * @param string $module Use like ModuleName or ModuleName/ModelName
 * @return object
 */
function model($module)
{
    $part = strpos($module, '/') !== false ? explode('/', $module) : [$module, $module];
    $class = 'Module\\'.$part[0].'\\Models\\'.$part[1];

    return app()->make($class);
}

function thumbnail($url, $width, $height, array $params = ['resize'])
{
    return $url;
}

/**
 * Returns submit button only if can edit source
 * @param string $file
 * @param string $button
 * @return string
 */
function saveButton($file = '', $button = '')
{
    if (!$file || (is_writable($file) && is_writable(dirname($file)))) {
        return $button ? $button : H::submitButton(t('save'), ['class' => 'btn btn-primary']);
    } else {
        return H::button(t('not_writable'), ['class' => 'btn btn-primary disabled']);
    }
}

function sydesErrorHandler($level, $message, $file = '', $line = 0)
{
    if (error_reporting() & $level) {
        throw new ErrorException($message, 0, $level, $file, $line);
    }
}
