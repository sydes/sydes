<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace System;

use FastRoute\Dispatcher;
use Psr\Container\ContainerInterface;
use Sydes\Document;
use Sydes\Exception\RedirectException;
use Sydes\Http\Redirect;
use Sydes\View\View;
use Zend\Diactoros\Response;

class Runner
{
    /** @var \DI\Container */
    protected $app;

    public function __construct(ContainerInterface $app)
    {
        $this->app = $app;

        session_start();
    }

    public function run($silent = false)
    {
        try {
            $response = $this->process();
        } catch (\Exception $e) {
            $response = $this->processException($e);
        }

        if (!$silent) {
            $this->app->get('emitter')->emit($response);
        }

        return $response;
    }

    private function process()
    {
        if (!model('Settings/App')->isCreated()) {
            return $this->execute(['Main@installer']);
        }

        date_default_timezone_set($this->app->get('app')->get('timeZone'));

        $path = '/'.ltrim($this->app->get('request')->getUri()->getPath(), '/');
        $this->app->set('section', ($path == '/admin' || strpos($path, '/admin/') === 0) ? 'admin' : 'front');

        $this->findSite();

        $this->findLocale($path);
        $this->app->get('translator')->init($this->app->get('locale'));

        $this->includeModules();

        $events = $this->app->get('event');
        $events->trigger('site.found');

        $route = $this->findRoute($path);

        $module = self::parseRoute($route[0]);
        $this->app->get('translator')->loadFrom('module', $module['path'][0]);

        $events->setContext(strtolower($this->app->get('section').'/'.
            implode('/', $module['path']).'/'.$module['method']));

        $events->trigger('route.found', [&$route]);

        $result = $this->execute($route);
        $events->trigger('module.executed', [&$result]);

        $response = $this->prepare($result);
        $events->trigger('response.prepared', [&$response]);

        return $response;
    }

    private function processException(\Exception $e)
    {
        $className = get_class($e);
        if ($pos = strrpos($className, '\\')) {
            $className = substr($className, $pos + 1);
        }

        $this->app->get('event')->trigger('exception.thrown', [$e], $className);

        $handler = $className.'Handler';

        if (!$this->app->has($handler)) {
            $handler = 'defaultErrorHandler';
        }
        $handler = $this->app->get($handler);

        return $handler($e);
    }

    private function findSite()
    {
        if ($this->app->get('section') == 'admin' && isset($_SESSION['site'])) {
            $this->app->set('siteId', $_SESSION['site']);

            return;
        }

        $domains = $this->app->get('cache')->remember('domains', function () {
            $sites = glob(app('dir.site').'/*', GLOB_ONLYDIR);
            $domains = [];
            foreach ($sites as $sitePath) {
                $config = include $sitePath.'/config.php';
                $site = str_replace(app('dir.site').'/', '', $sitePath);
                foreach ($config['domains'] as $domain) {
                    $domains[$domain] = $site;
                }
            }
            return $domains;
        }, 31536000);

        $host = $this->app->get('request')->getUri()->getHost();
        if (!isset($domains[$host])) {
            abort(400, 'Site not found');
        }

        $this->app->set('site.id', $domains[$host]);

        $mainDomain = $this->app->get('site')->get('domains')[0];
        if ($this->app->get('section') == 'front' && $mainDomain != $host &&
            $this->app->get('site')->get('onlyMainDomain')
        ) {
            throw new RedirectException('http://'.$mainDomain.$this->app->get('request')->getUri()->getPath());
        }
    }

    private function findLocale(&$path)
    {
        if ($this->app->get('section') == 'admin') {
            $this->app->set('locale', $this->app->get('app')->get('locale'));
        } else {
            $locales = $this->app->get('site')->get('locales');
            $this->app->set('locale', $locales[0]);

            if (count($locales) > 1) {

                if ($this->app->get('site')->get('localeIn') == 'url') {
                    if ($path == '/') {
                        throw new RedirectException('/'.$locales[0]);
                    }

                    $pathParts = explode('/', $path, 3);

                    if (in_array($pathParts[1], $locales)) {
                        $this->app->set('locale', $pathParts[1]);
                        unset($pathParts[1]);
                        $path = count($pathParts) > 1 ? implode('/', $pathParts) : '/';
                    }
                } else {
                    $host = $this->app->get('request')->getUri()->getHost();
                    if (isset($this->app->get('site')->get('host2locale')[$host])) {
                        $this->app->set('locale', $this->app->get('site')->get('host2locale')[$host]);
                    }
                }
            }
        }
    }

    private function includeModules()
    {
        $events = $this->app->get('event');
        foreach ($this->app->get('site')->get('modules') as $name => $module) {
            $dir = moduleDir($name);

            if (isset($module['handlers']) && file_exists($dir.'/Handlers.php')) {
                $class = 'Module\\'.$name.'\\Handlers';
                new $class($events);
            }

            if (isset($module['files'])) {
                foreach ($module['files'] as $file) {
                    include $dir.'/functions/'.$file;
                }
            }
        }
    }

    private function findRoute($path)
    {
        $router = $this->app->get('router');
        if ($this->app->get('settings')['cacheRouter']) {
            $router->setCacheFile($this->app->get('dir.cache.route'));
        }

        $modules = array_keys($this->app->get('site')->get('modules'));
        $sys = $this->app->get('dir.system').'/modules/';
        $usr = $this->app->get('dir.module').'/';
        $files = [];
        foreach ($modules as $module) {
            $files[] = $sys.$module.'/routes/web.php';
            $files[] = $usr.$module.'/routes/web.php';
        }
        $routeInfo = $router->dispatch(
            $files,
            $this->app->get('request')->getMethod(),
            $path
        );

        if ($routeInfo[0] == Dispatcher::FOUND) {
            return [$routeInfo[1], $routeInfo[2]];
        } elseif (strpos($path, '.')) {
            return ['Main@error', ['code' => 404]];
        }

        return model('Route')->findOrFail($path);
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
            'path'   => explode('/', $parts[0]),
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
    public function execute($params)
    {
        $route = self::parseRoute($params[0]);

        $path = moduleDir($route['path'][0]);
        if (!$path) {
            throw new \Exception(t('error_module_folder_not_found', ['module' => $route['path'][0]]));
        }

        if (count($route['path']) > 1) {
            $class = 'Module\\'.implode('\\', $route['path']).'Controller';
        } else {
            $class = 'Module\\'.$route['path'][0].'\Controller';
        }

        if (!class_exists($class)) {
            throw new \Exception(t('error_class_not_found', ['class' => $class]));
        }

        $instance = $this->app->make($class);
        if (!method_exists($instance, $route['method'])) {
            throw new \Exception(t('error_method_not_found', [
                'class'  => $class,
                'method' => $route['method'],
            ]));
        }

        return $this->app->call([$instance, $route['method']], ifsetor($params[1], []));
    }

    private function prepare($content)
    {
        if ($content instanceof Redirect && app('request')->isAjax()) {
            return json(['redirect' => $content->getUri()]);
        } elseif ($content instanceof Response) {
            return $content;
        } elseif ($content instanceof Document) {
            return html($this->app->get('renderer')->render($content));
        } elseif ($content instanceof View) {
            return html($content->render());
        } elseif (is_array($content)) {
            if (isset($content['alerts'])) {
                $_SESSION['alerts'] = [];
            }

            if (isset($content['notify'])) {
                unset($_SESSION['notify']);
            }

            return json($content);
        }
        return text((string)$content);
    }
}
