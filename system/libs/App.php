<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

use FastRoute\Dispatcher;
use Zend\Diactoros\Response;

class App
{
    /** @var Container */
    private $container;

    public function __construct(array $values = [])
    {
        session_start();
        mb_internal_encoding('UTF-8');

        $this->container = new Container($values);
        Container::setContainer($this->container);

        $this->handleErrors();
    }

    public function run($silent = false)
    {
        if (!$this->loadConfig()) {
            $this->container['emitter']->emit(self::execute(['Main@installer']));
            return null;
        }

        date_default_timezone_set($this->container['app']['timeZone']);

        if (!$site = $this->findSite()) {
            abort(400, 'Site not found');
        }

        $path = '/'.ltrim($this->container['request']->getUri()->getPath(), '/');

        $this->container['section'] = ($path == '/admin' || strpos($path, '/admin/') === 0) ? 'admin' : 'front';

        $this->findLocale($path);
        $this->container['translator']->init($this->container['locale']);

        $route = $this->findRoute($site, $path);

        $module = self::parseRoute($route[0]);
        $this->container['translator']->loadFrom('module', $module['path'][0]);

        $events = $this->container['event'];
        $events->setContext(strtolower($this->container['section'].'/'.
            implode('/', $module['path']).'/'.$module['method']));

        $events->trigger('route.found', [&$route]);

        $result = self::execute($route);
        $events->trigger('module.executed', [&$result]);

        $response = $this->prepare($result);
        $events->trigger('response.prepared', [&$response]);

        if (!$silent) {
            $this->container['emitter']->emit($response);
        }

        return $response;
    }

    private function handleErrors()
    {
        error_reporting(-1);
        set_error_handler(function ($level, $message, $file = '', $line = 0) {
            if (error_reporting() & $level) {
                throw new \ErrorException($message, 0, $level, $file, $line);
            }
        });

        $c = $this->container;
        set_exception_handler(function ($e) use ($c) {
            $c['event']->trigger('exception.thrown', [$e], get_class($e));
            $c['emitter']->emit($c['exceptionHandler']->render($e, $c['settings']['showErrorInfo']));
        });
    }

    private function findRoute($site, $path)
    {
        $router = $this->container['router'];
        if ($this->container['settings']['cacheRouter']) {
            $router->setCacheFile(DIR_CACHE.'/routes.'.$site.'.cache');
        }

        $routeInfo = $router->dispatch(
            array_keys($this->container['site']['modules']),
            $this->container['request']->getMethod(),
            $path
        );

        if ($routeInfo[0] == Dispatcher::FOUND) {
            return [$routeInfo[1], $routeInfo[2]];
        } elseif (strpos($path, '.')) {
            return ['Main@error', ['code' => 404]];
        }

        return model('route')->findOrFail($path);
    }

    private function loadConfig()
    {
        if (!file_exists(DIR_APP.'/config.php')) {
            return false;
        }

        $config = include DIR_APP.'/config.php';
        $this->container['rawAppConfig'] = $config;
        $this->container['app'] = $config['app'];

        return true;
    }

    private function findSite()
    {
        $domains = $this->container['cache']->remember('domains', function () {
            $sites = glob(DIR_SITE.'/s*', GLOB_ONLYDIR);
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

        $host = $this->container['request']->getUri()->getHost();
        if (!isset($domains[$host])) {
            return false;
        }

        $site = $domains[$host];

        $siteConf = include DIR_SITE.'/'.$site.'/config.php';
        $this->container['rawSiteConfig'] = $siteConf;
        $this->container['site'] = ['id' => $site] + $siteConf;

        $events = $this->container['event'];
        foreach ($siteConf['modules'] as $name => $module) {
            if (isset($module['handlers'])) {
                foreach ($module['handlers'] as $handler) {
                    call_user_func_array($handler, [$events]);
                }
            }

            if (isset($module['files'])) {
                foreach ($module['files'] as $file) {
                    include moduleDir($name).'/'.$file;
                }
            }
        }

        return $site;
    }

    /**
     * Splits ModuleName/Submodule[at]method to array for autoLoader
     *
     * @param string $route
     * @return array
     */
    public static function parseRoute($route)
    {
        $parts = explode('@', $route);
        $array = [
            'path' => explode('/', $parts[0]),
            'method' => $parts[1],
        ];
        return $array;
    }

    /**
     * Executes passed handler with variables
     *
     * @param array $params ['class@method', ['name' => 'var', ...]]
     * @return mixed
     * @throws \Exception
     */
    public static function execute($params)
    {
        $route = self::parseRoute($params[0]);

        $path = moduleDir($route['path'][0]);
        if (is_null($path)) {
            throw new \Exception(t('error_module_folder_not_found', ['module' => $route['path'][0]]));
        }

        if (count($route['path']) > 1) {
            $class = 'Module\\'.implode('\\', $route['path']).'Controller';
        } else {
            $class = 'Module\\'.$route['path'][0].'\Controller';
        }
        $instance = new $class;
        return call_user_func_array([$instance, $route['method']], ifsetor($params[1], []));
    }

    private function prepare($content)
    {
        if ($content instanceof Response) {
            return $content;
        } elseif ($content instanceof Document) {
            return html($this->container['renderer']->render($content));
        } elseif ($content instanceof View) {
            return html((string)$content);
        } elseif (is_array($content)) {
            return json($content);
        }
        return text((string)$content);
    }

    private function findLocale(&$path)
    {
        if ($this->container['section'] == 'admin') {
            $this->container['locale'] = $this->container['app']['locale'];
        } else {
            $locales = $this->container['site']['locales'];
            $this->container['locale'] = $locales[0];

            if (count($locales) > 1) {

                if ($this->container['site']['localeIn'] == 'url') {
                    if ($path == '/') {
                        throw new \App\Exception\RedirectException('/'.$locales[0]);
                    }

                    $pathParts = explode('/', $path, 3);

                    if (in_array($pathParts[1], $locales)) {
                        $this->container['locale'] = $pathParts[1];
                        unset($pathParts[1]);
                        $path = count($pathParts) > 1 ? implode('/', $pathParts) : '/';
                    }
                } else {
                    $host = $this->container['request']->getUri()->getHost();
                    if (isset($this->container['site']['host2locale'][$host])){
                        $this->container['locale'] = $this->container['site']['host2locale'][$host];
                    }
                }
            }
        }
    }
}
