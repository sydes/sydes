<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

define('DIR_ROOT', dirname(__DIR__));
define('DIR_APP',    DIR_ROOT.'/app');
define('DIR_SYSTEM', DIR_ROOT.'/system');
define('DIR_THEME',  DIR_ROOT.'/themes');
define('DIR_VENDOR', DIR_ROOT.'/vendor');
define('DIR_CACHE',  DIR_APP.'/cache');
define('DIR_IBLOCK', DIR_APP.'/iblocks');
define('DIR_L10N',   DIR_APP.'/l10n');
define('DIR_LOG',    DIR_APP.'/logs');
define('DIR_MODULE', DIR_APP.'/modules');
define('DIR_SITE',   DIR_APP.'/sites');
define('DIR_TEMP',   DIR_APP.'/temp');

define('SYDES_VERSION', '3.0.0-b1');
define('APP_START', microtime(true));

if (!defined('DIR_UPLOAD')) {
    define('DIR_UPLOAD', DIR_ROOT.'/upload');
}
if (!defined('DIR_THUMB')) {
    define('DIR_THUMB', DIR_APP.'/thumbs');
}
