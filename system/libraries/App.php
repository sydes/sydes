<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

use App\Exception\NotFoundHttpException;
use App\Exception\ForbiddenHttpException;
use App\Exception\HttpException;

class App extends \Pimple\Container {

    private static $instance;

    private function __clone() {}

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialization of application
     *
     */
    public function init() {
        $this['preferredLanguage'] = $this['request']->getPreferredLanguage($this['translator']->installedPackages);
        // TODO выяснить языки админки и фронта
        // установить основкую локаль
        // загрузить языковые пакеты

        $this['section'] = (strpos($this['request']->url, ADMIN.'/') === 1) ? 'admin' : 'front';

        $this['renderer'] = function ($c) {
            return $c['section'] == 'admin' ? new Renderer\Admin() : new Renderer\Front();
        };

        // load main languages

        // find site by domain
        // load site config
        
        $this['config'] = include DIR_APP.'/config.php';

        date_default_timezone_set($this['config']['app']['time_zone']);

        $site = 's1';

        $this['db'] = function () use ($site) {
            return new Database($site);
        };
    }

    public function run() {
        $result = $this->sendRequestThroughRouter($this['request']);

        if ($result instanceof Http\Response) {
            return $result;
        } else {
            return response($result);
        }
    }

    /**
     * Throw an HttpException with the given data.
     *
     * @param int $code
     * @param string $message
     * @throws Exception\HttpException
     */
    public function abort($code, $message = null) {
        if ($code == 404) {
            throw new NotFoundHttpException($message);
        } elseif ($code == 403) {
            throw new ForbiddenHttpException($message);
        }
        throw new HttpException($code, $message);
    }

    private function sendRequestThroughRouter($request) {
        $dispatcher = \FastRoute\cachedDispatcher(function(\FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/page/{id:[0-9]+}', 'test/page');
            $r->addRoute('GET', '/notfound', 'test/notfound');
            $r->addRoute('GET', '/forbidden', 'test/forbidden');
            $r->addRoute('GET', '/ajax', 'test/ajax');
            $r->addRoute('GET', '/string.txt', 'test/string');
            $r->addRoute('GET', '/export', 'test/export');
            $r->addRoute('GET', '/html', 'test/html');
            $r->addRoute('GET', '/nool', 'test/nool');
            $r->addRoute('GET', '/moved', 'test/moved');
            $r->addRoute('GET', '/update', 'test/update');
            $r->addRoute('GET', '/store', 'test/store');
            $r->addRoute('GET', '/ajaxupdate', 'test/ajaxupdate');
            $r->addRoute('GET', '/ajaxstore', 'test/ajaxstore');
            $r->addRoute('GET', '/', 'test/index');
        }, ['cacheFile' => DIR_CACHE.'/route.cache']);

        $routeInfo = $dispatcher->dispatch($request->method, $request->url);

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::FOUND:
                $parts = explode('/', $routeInfo[1]);
                $vars = $routeInfo[2];
                break;
            default:
                //TODO try to find predefined urls in database
                if (1){
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

}
