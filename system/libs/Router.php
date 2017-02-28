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
     * Path to fast route cache file.
     */
    protected $cacheFile = false;

    /** @var \FastRoute\Dispatcher */
    protected $dispatcher;

    /**
     * @param string $file
     */
    public function setCacheFile($file)
    {
        $this->cacheFile = $file;
    }

    /**
     * Dispatch router for request
     *
     * @param $method
     * @param $uri
     * @return array
     * @link   https://github.com/nikic/FastRoute/blob/master/src/Dispatcher.php
     */
    public function dispatch($modules, $method, $uri)
    {
        $callback = function (RouteCollector $r) use ($modules) {
            foreach ($modules as $module) {
                $class = 'Module\\'.$module.'\\Controller';
                if (isset($class::$routes)) {
                    foreach ($class::$routes as $route) {
                        $r->addRoute($route[0], $route[1], $route[2]);
                    }
                }
            }
        };

        return $this->createDispatcher($callback)->dispatch($method, $uri);
    }

    /**
     * @return \FastRoute\Dispatcher
     */
    protected function createDispatcher($callback)
    {
        $this->dispatcher = \FastRoute\cachedDispatcher($callback, [
            'cacheFile'     => $this->cacheFile,
            'cacheDisabled' => is_bool($this->cacheFile),
        ]);

        return $this->dispatcher;
    }
}
