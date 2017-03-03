<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

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
        $callback = function (Route $r) use ($modules) {
            foreach ($modules as $module) {
                $class = 'Module\\'.$module.'\\Controller';
                if (method_exists($class, 'routes')) {
                    $class::routes($r);
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
            'routeCollector' => 'App\\Route',
        ]);

        return $this->dispatcher;
    }
}
