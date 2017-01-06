<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

use FastRoute\RouteCollector;

class Router
{
    /**
     * Path to fast route cache file. Set to false to disable route caching
     *
     * @var string|False
     */
    protected $cacheFile = false;

    /** @var \FastRoute\Dispatcher */
    protected $dispatcher;

    protected $site;
    protected $cacheDisabled = true;

    /**
     * @param string $site
     */
    public function forSite($site)
    {
        $this->site = $site;
        $this->cacheFile = DIR_CACHE.'/routes.'.$site.'.cache';
    }

    /**
     * Set path to fast route cache file. If this is false then route caching is disabled.
     *
     * @param bool $need
     */
    public function cache($need)
    {
        if ($need && !is_writable(dirname($this->cacheFile))) {
            throw new \RuntimeException('Router cacheFile directory must be writable');
        }

        $this->cacheDisabled = !$need;
    }

    /**
     * Dispatch router for request
     *
     * @param $method
     * @param $uri
     * @return array
     * @link   https://github.com/nikic/FastRoute/blob/master/src/Dispatcher.php
     */
    public function dispatch($method, $uri)
    {
        return $this->createDispatcher()->dispatch($method, $uri);
    }

    /**
     * @return \FastRoute\Dispatcher
     */
    protected function createDispatcher()
    {
        $routeDefinitionCallback = function (RouteCollector $r) {
            $routes = include DIR_SYSTEM.'/routes.php';
            foreach (app('site')['routes'] as $extRoutes) {
                $routes = array_merge($routes, $extRoutes);
            }

            foreach ($routes as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        };

        $this->dispatcher = \FastRoute\cachedDispatcher($routeDefinitionCallback, [
            'cacheFile'     => $this->cacheFile,
            'cacheDisabled' => $this->cacheDisabled,
        ]);

        return $this->dispatcher;
    }
}
