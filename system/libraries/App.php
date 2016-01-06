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

    /**
     * Initialization of application
     *
     */
    public function init()
    {
        $this['event']->trigger('before.system.init');

        $this['config'] = include DIR_APP.'/config.php';

        $this['adminLang'] = $this['config']['app']['language'];
        $this['translator']->loadPackage($this['adminLang']);

        $domains = $this->findDomains();
        $site = $this['site'] = $domains[$this['request']->domain];
        $siteConf = include DIR_SITE.'/'.$site.'/config.php';
        $this['config'] = array_merge($this['config'], ['site' => $siteConf]);
        $this['base'] = $siteConf['domains'][0];
        $this['db'] = function () use ($site) {
            return new Database($site);
        };

        $this['section'] = (strpos($this['request']->url, ADMIN.'/') === 1) ? 'admin' : 'front';
        $this['contentLang'] = $this->findContentLocale();
        $this['renderer'] = function ($c) {
            return $c['section'] == 'admin' ? new Renderer\Admin() : new Renderer\Front();
        };

        date_default_timezone_set($this['config']['app']['time_zone']);

        $this['event']->trigger('after.system.init');
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
        } elseif ($result instanceof Document) {
            if (isset($_SESSION['notify'])) {
                $result->notify = $_SESSION['notify'];
                unset($_SESSION['notify']);
            }
            if (isset($_SESSION['alerts'])) {
                $result->alerts = $_SESSION['alerts'];
                unset($_SESSION['alerts']);
            }
        }

        return response($result);
    }

    /**
     * Throw an HttpException with the given data.
     *
     * @param int    $code
     * @param string $message
     * @throws Exception\HttpException
     */
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
        }, ['cacheFile' => DIR_CACHE.'/route.cache']);

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

    private function findDomains()
    {
        $domains = $this['cache']->remember('domains', function () {
            $domains = [];
            foreach (glob(DIR_SITE.'/s*', GLOB_ONLYDIR) as $sitePath) {
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
