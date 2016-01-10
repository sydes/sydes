<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

use App\Exception\ForbiddenHttpException;
use App\Exception\HttpException;
use App\Exception\NotFoundHttpException;

class App extends \Pimple\Container
{

    private static $instance;

    private function __clone() {}

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init()
    {
        $this['event']->trigger('before.system.init', [$this]);

        if (!file_exists(DIR_APP.'/config.php')) { // not yet installed
            $folders = ['cache', 'iblocks', 'languages', 'logs', 'modules', 'plugins', 'sites', 'temp', 'thumbs'];
            foreach ($folders as $folder) {
                if (!file_exists(DIR_APP.'/'.$folder)) {
                    mkdir(DIR_APP.'/'.$folder, 0777, true);
                }
            }
            $this['request']->url = '/install';
            return;
        }

        $this['translator']->loadPackage();

        $this['config'] = include DIR_APP.'/config.php';
        $this['user'] = function () {
            return new User($this['config']['user']);
        };

        $this['adminLang'] = $this['config']['app']['language'];
        $this['translator']->loadPackage($this['adminLang']);

        $sites = glob(DIR_SITE.'/s*', GLOB_ONLYDIR);
        $domains = $this->findDomains($sites);
        $site = $this['site'] = $domains[$this['request']->domain];

        $siteConf = include DIR_SITE.'/'.$site.'/config.php';
        $this['config'] = array_merge($this['config'], ['site' => $siteConf]);
        $this['base'] = $siteConf['domains'][0];
        $this['db'] = function () use ($site) {
            return new Database($site);
        };

        $this['contentLang'] = $this->findContentLocale();

        date_default_timezone_set($this['config']['app']['time_zone']);

        if ($this['request']->isPost && $_SESSION['csrf_token'] != $this['request']->get('token')) {
            abort(403, t('invalid_csrf_token'));
        }

        $this['event']->trigger('after.system.init', [$this]);
    }

    public function run()
    {
        $result = $this->sendRequestThroughRouter($this['request']);

        if ($result instanceof Http\Response) {
            return $result;
        }

        if (is_array($result)) {
            if (isset($result['notify'])) {
                unset($_SESSION['notify']);
            }
            if (isset($result['alerts'])) {
                unset($_SESSION['alerts']);
            }
        }

        return response($result);
    }

    public function abort($code, $message = null)
    {
        if ($code == 404) {
            throw new NotFoundHttpException($message);
        } elseif ($code == 403) {
            throw new ForbiddenHttpException($message);
        }
        throw new HttpException($code, $message);
    }

    private function sendRequestThroughRouter($request)
    {
        $dispatcher = \FastRoute\cachedDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/page/{id:[0-9]+}', 'test/page');
            $r->addRoute('GET', '/notfound', 'test/notfound');
            $r->addRoute('GET', '/forbidden', 'test/forbidden');
            $r->addRoute('GET', '/ajax', 'test/ajax');
            $r->addRoute('GET', '/string.txt', 'test/string');
            $r->addRoute('GET', '/export', 'test/export');
            $r->addRoute('GET', '/html', 'test/html');
            $r->addRoute('GET', '/nool', 'test/nool');
            $r->addRoute('GET', '/moved', 'test/moved');
            $r->addRoute('GET', '/update', 'test/notifyAfterRedirect');
            $r->addRoute('GET', '/store', 'test/alertAfterRedirect');
            $r->addRoute('GET', '/ajaxupdate', 'test/ajaxNotify');
            $r->addRoute('GET', '/ajaxstore', 'test/ajaxAlert');
            $r->addRoute('GET', '/refresh', 'test/ajaxRefresh');
            $r->addRoute('GET', '/refresh2', 'test/refreshAndNotify');
            $r->addRoute('GET', '/random', 'test/random');
            $r->addRoute('GET', '/', 'test/index');
            $r->addRoute('GET', '/admin', 'test/adminMain');
            $r->addRoute('GET', '/admin/pages', 'test/adminPages');

            $r->addRoute('GET', '/login', 'user/loginForm');
            $r->addRoute('POST', '/login', 'user/login');
            $r->addRoute('GET', '/install', 'utils/signUpForm');
            $r->addRoute('POST', '/install', 'utils/signUp');
            $r->addRoute('GET', '/admin/sites/add', 'sites/addForm');
            $r->addRoute('POST', '/admin/sites/add', 'sites/add');
        }, ['cacheFile' => DIR_CACHE.'/routes.cache']);

        $routeInfo = $dispatcher->dispatch($request->method, $request->url);

        if ($routeInfo[0] == \FastRoute\Dispatcher::FOUND) {
            $parts = explode('/', $routeInfo[1]);
            $vars = $routeInfo[2];
        } else {
            //TODO try to find predefined urls in database
            if (1) {
                throw new NotFoundHttpException;
            }
            $parts = explode('/', 'test/page/42');
            $vars = array_slice($parts, 2);
        }

        $name = $parts[0];
        $class = ucfirst($parts[0]).'Controller';
        $method = $parts[1];

        if (null === ($path = findExt('module', $name))) {
            trigger_error(sprintf(t('module_folder_not_found'), $name), E_USER_ERROR);
        }
        include $path.'/index.php';
        $instance = new $class;
        return call_user_func_array([$instance, $method], $vars);
    }

    private function findDomains($sites)
    {
        $domains = $this['cache']->remember('domains', function () use ($sites) {
            $domains = [];
            foreach ($sites as $sitePath) {
                $config = include $sitePath.'/config.php';
                $site = str_replace(DIR_SITE.'/', '', $sitePath);
                foreach ($config['domains'] as $domain) {
                    $domains[$domain] = $site;
                }
            }
            return $domains;
        }, 31536000);

        if (!isset($domains[$this['request']->domain])) {
            $this->abort(404, t('error_domain_not_associated'));
        }

        return $domains;
    }

    private function findContentLocale()
    {
        $locales = $this['config']['site']['locales'];
        $locale = $locales[0];

        if (count($locales) > 1) {
            if ($this['section'] == 'admin') {
                if (isset($this['request']->cookies['content_locale']) &&
                    in_array($this['request']->cookies['content_locale'], $locales)
                ) {
                    $locale = $this['request']->cookies['content_locale'];
                }
            } else {
                $url = explode('/', $this['request']->url, 3);
                if (in_array($url[1], $locales)) {
                    $locale = $url[1];
                    $this['request']->url = isset($url[2]) ? substr($this['request']->url, 3) : '/';
                }
            }
        }

        return $locale;
    }

}
