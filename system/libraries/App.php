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


        // load main config and languages

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
            $r->addRoute('GET', '/page/{id:[0-9]+}', 'Test@page');
            $r->addRoute('GET', '/notfound', 'Test@notfound');
            $r->addRoute('GET', '/forbidden', 'Test@forbidden');
            $r->addRoute('GET', '/ajax', 'Test@ajax');
            $r->addRoute('GET', '/string.txt', 'Test@string');
            $r->addRoute('GET', '/export', 'Test@export');
            $r->addRoute('GET', '/html', 'Test@html');
            $r->addRoute('GET', '/nool', 'Test@nool');
            $r->addRoute('GET', '/moved', 'Test@moved');
            $r->addRoute('GET', '/update', 'Test@update');
            $r->addRoute('GET', '/store', 'Test@store');
            $r->addRoute('GET', '/ajaxupdate', 'Test@ajaxupdate');
            $r->addRoute('GET', '/ajaxstore', 'Test@ajaxstore');
            $r->addRoute('GET', '/', 'Test@index');
        }, ['cacheFile' => DIR_CACHE.'/route.cache']);

        $routeInfo = $dispatcher->dispatch($request->method, $request->url);

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::FOUND:
                $parts = explode('@', $routeInfo[1]);
                $name = strtolower($parts[0]);
                $class = $parts[0].'Controller';
                $method = $parts[1];
                $vars = $routeInfo[2];
                break;
            default:
                //TODO try to find predefined urls in database
                if (1){
                    throw new NotFoundHttpException;
                }
                $parts = explode('/', 'test/page/42');
                $name = $parts[0];
                $class = ucfirst($parts[0]).'Controller';
                $method = $parts[1];
                $vars = array_slice($parts, 2);
        }

        if (null === ($path = findPath('module', $name))) {
            throw new \RuntimeException(t('module_folder_not_found'));
        }
        include $path;
        $instance = new $class;
        return call_user_func_array([$instance, $method], $vars);
    }

}
