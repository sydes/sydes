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

        $this->includeModules($this->app->get('modules')->get());

        $events = $this->app->get('event');
        $events->trigger('site.found');

        $route = $this->findRoute($path);

        $module = $this->parseRoute($route[0]);
        $this->app->get('translator')->loadFrom('module', $module['path'][0]);

        $events->setContext(strtolower($this->app->get('section').'/'.
            implode('/', $module['path']).'/'.$module['method']));

        $events->trigger('route.found', [&$route]);

        $result = $this->execute([$module, $route[1]]);
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
            $this->app->set('site.id', $_SESSION['site']);

            return;
        }

        $dir = $this->app->get('dir.site');
        $domains = $this->app->get('cache')->remember('domains', function () use ($dir) {
            $sites = glob($dir.'/*', GLOB_ONLYDIR);
            $domains = [];
            foreach ($sites as $sitePath) {
                $config = include $sitePath.'/config.php';
                $site = str_replace($dir.'/', '', $sitePath);
                foreach ($config['domains'] as $domain) {
                    $domains[$domain] = $site;
                }
            }

            return $domains;
        }, 31536000);

        if (empty($domains)) {
            $this->app->set('site.id', 1);

            return;
        }

        $host = $this->app->get('request')->getUri()->getHost();
        if (!isset($domains[$host])) {
            if ($this->app->get('section') == 'admin') {
                $this->app->set('site.id', 1);

                return;
            }
            abort(400, 'Site not found');
        }

        $this->app->set('site.id', $domains[$host]);

        $mainDomain = $this->app->get('site')->get('domains')[0];
        if ($this->app->get('section') == 'front' && $mainDomain != $host &&
            $this->app->get('site')->get('onlyMainDomain')
        ) {
            throw new RedirectException((string)$this->app->get('request')->getUri()->withHost($mainDomain));
        }
    }

    private function findLocale(&$path)
    {
        if ($this->app->get('section') == 'admin') {
            $this->app->set('locale', $this->app->get('app')->get('adminLanguage'));

            return;
        }

        $siteConf = $this->app->get('site');
        $locales = $siteConf->get('locales');
        $this->app->set('locale', $locales[0]);

        if (count($locales) > 1) {
            if ($siteConf->get('localeIn') == 'url') {
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
                $h2l = $siteConf->get('host2locale');
                if (isset($h2l[$host])) {
                    $this->app->set('locale', $h2l[$host]);
                }
            }
        }
    }

    private function includeModules($modules)
    {
        $events = $this->app->get('event');
        foreach ($modules as $name => $module) {
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

        $modules = array_keys($this->app->get('modules')->get());
        $sys = $this->app->get('dir.system').'/modules/';
        $usr = $this->app->get('dir.module').'/';
        $files = [];
        foreach ($modules as $module) {
            $files[] = $sys.$module.'/routes/web.php';
            $files[] = $usr.$module.'/routes/web.php';
        }
        $routeInfo = $router->dispatch(
            $files,
            $this->app->get('request')->method(),
            $path
        );

        if ($routeInfo[0] == Dispatcher::FOUND) {
            return [$routeInfo[1], $routeInfo[2]];
        } elseif (strpos($path, '.')) {
            return ['Main@error', ['code' => 404]];
        }

        return model('Route')->find($path);
    }

    /**
     * Splits ModuleName/Submodule[at]method to array for autoLoader
     *
     * @param string $route
     * @return array
     */
    public function parseRoute($route)
    {
        $parts = explode('@', $route);
        $action = explode('?', $parts[1]);
        $array = [
            'path'   => explode('/', $parts[0]),
            'method' => $action[0],
            'params' => [],
        ];

        if (!isset($array['path'][1])) {
            $array['path'][1] = 'Index';
        }

        if (isset($action[1])) {
            parse_str($action[1], $array['params']);
        }

        return $array;
    }

    /**
     * Executes passed handler with variables
     *
     * @param array $params ['class@method?param=value', ['name' => 'var', ...]]
     * @return mixed
     * @throws \Exception
     */
    public function execute($params)
    {
        $route = $params[0];

        if (!moduleDir($route['path'][0])) {
            throw new \Exception(t('error_module_folder_not_found', ['module' => $route['path'][0]]));
        }

        $class = 'Module\\'.$route['path'][0].'\Controllers\\'.$route['path'][1].'Controller';

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

        return $this->app->call([$instance, $route['method']], array_merge($route['params'], ifsetor($params[1], [])));
    }

    private function prepare($content)
    {
        if ($content instanceof Redirect && app('request')->ajax()) {
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
