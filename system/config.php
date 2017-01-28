<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

define('DIR_ROOT',     dirname(__DIR__));
define('DIR_SYSTEM',   DIR_ROOT.'/system');
define('DIR_VENDOR',   DIR_ROOT.'/vendor');
define('DIR_THEME',    DIR_ROOT.'/themes');
define('DIR_APP',      DIR_ROOT.'/app');
define('DIR_CACHE',    DIR_APP.'/cache');
define('DIR_IBLOCK',   DIR_APP.'/iblocks');
define('DIR_LANGUAGE', DIR_APP.'/languages');
define('DIR_LOG',      DIR_APP.'/logs');
define('DIR_MODULE',   DIR_APP.'/modules');
define('DIR_PLUGIN',   DIR_APP.'/plugins');
define('DIR_SITE',     DIR_APP.'/sites');
define('DIR_TEMP',     DIR_APP.'/temp');
define('DIR_THUMB',    DIR_APP.'/thumbs');

define('API_HOST', 'http://api.sydes.ru/');

define('SYDES_VERSION', '3.0.0-a');
define('APP_START', microtime(true));
