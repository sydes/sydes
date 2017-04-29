<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

use FastRoute\Dispatcher;
use Sydes\Exception\RedirectException;
use Sydes\Http\Redirect;
use Zend\Diactoros\Response;

class App
{
    /** @var Container */
    private $container;
    private $defaultSettings = [
        'cacheRouter'  => true,
        'debugLevel'   => 0,
        'checkUpdates' => true,
    ];

    public function __construct(array $values = [])
    {
        session_start();
        mb_internal_encoding('UTF-8');

        error_reporting(-1);
        set_error_handler('sydesErrorHandler');

        $values['settings'] = array_merge($this->defaultSettings, ifsetor($values['settings'], []));
        $values['section'] = 'base';

        $this->container = new Container($values, ['namespaces' => ['Sydes']]);

        $this->container->register(new DefaultServicesProvider);
        $this->container->register(new ExceptionHandlersProvider);

        Container::setContainer($this->container);

        class_alias('Sydes\Html\BS4','H');
        class_alias('Sydes\Html\FormBuilder','Form');
    }

    public function run($silent = false)
    {
        try {
            $response = $this->process();
        } catch (\Exception $e) {
            $response = $this->processException($e);
        }

        if (!$silent) {
            $this->container['emitter']->emit($response);
        }

        return $response;
    }

    private function process()
    {
        if (!file_exists(DIR_APP.'/config.php')) {
            return $this->execute(['Main@installer']);
        }

        date_default_timezone_set($this->container['app']->get('timeZone'));

        $path = '/'.ltrim($this->container['request']->getUri()->getPath(), '/');
        $this->container['section'] = ($path == '/admin' || strpos($path, '/admin/') === 0) ? 'admin' : 'front';

        $this->findSite();

        $this->findLocale($path);
        $this->container['translator']->init($this->container['locale']);

        $this->includeModules();

        $events = $this->container['event'];
        $events->trigger('site.found');

        $route = $this->findRoute($path);

        $module = self::parseRoute($route[0]);
        $this->container['translator']->loadFrom('module', $module['path'][0]);

        $events->setContext(strtolower($this->container['section'].'/'.
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

        $this->container['event']->trigger('exception.thrown', [$e], $className);

        $handler = $className.'Handler';

        if (!isset($this->container[$handler])) {
            $handler = 'defaultErrorHandler';
        }

        return $this->container[$handler]($e);
    }

    private function findSite()
    {
        if ($this->container['section'] == 'admin' && isset($_SESSION['site'])) {
            $this->container['siteId'] = $_SESSION['site'];

            return;
        }

        $domains = $this->container['cache']->remember('domains', function () {
            $sites = glob(DIR_SITE.'/*', GLOB_ONLYDIR);
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
            abort(400, 'Site not found');
        }

        $this->container['siteId'] = $domains[$host];

        $mainDomain = $this->container['site']->get('domains')[0];
        if ($this->container['section'] == 'front' && $mainDomain != $host &&
            $this->container['site']->get('onlyMainDomain')) {
            throw new RedirectException('http://'.$mainDomain.$this->container['request']->getUri()->getPath());
        }
    }

    private function findLocale(&$path)
    {
        if ($this->container['section'] == 'admin') {
            $this->container['locale'] = $this->container['app']->get('locale');
        } else {
            $locales = $this->container['site']->get('locales');
            $this->container['locale'] = $locales[0];

            if (count($locales) > 1) {

                if ($this->container['site']->get('localeIn') == 'url') {
                    if ($path == '/') {
                        throw new RedirectException('/'.$locales[0]);
                    }

                    $pathParts = explode('/', $path, 3);

                    if (in_array($pathParts[1], $locales)) {
                        $this->container['locale'] = $pathParts[1];
                        unset($pathParts[1]);
                        $path = count($pathParts) > 1 ? implode('/', $pathParts) : '/';
                    }
                } else {
                    $host = $this->container['request']->getUri()->getHost();
                    if (isset($this->container['site']->get('host2locale')[$host])){
                        $this->container['locale'] = $this->container['site']->get('host2locale')[$host];
                    }
                }
            }
        }
    }

    private function includeModules()
    {
        $events = $this->container['event'];
        foreach ($this->container['site']->get('modules') as $name => $module) {
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
        $router = $this->container['router'];
        if ($this->container['settings']['cacheRouter']) {
            $router->setCacheFile(DIR_CACHE.'/routes.'.$this->container['siteId'].'.cache');
        }

        $routeInfo = $router->dispatch(
            array_keys($this->container['site']->get('modules')),
            $this->container['request']->getMethod(),
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

        $instance = $this->container->instantiate($class);
        if (!method_exists($instance, $route['method'])) {
            throw new \Exception(t('error_method_not_found', [
                'class' => $class,
                'method' => $route['method']
            ]));
        }

        return $this->container->call([$instance, $route['method']], ifsetor($params[1], []));
    }

    private function prepare($content)
    {
        if ($content instanceof Redirect && app('request')->isAjax()) {
            return json(['redirect' => $content->getUri()]);
        } elseif ($content instanceof Response) {
            return $content;
        } elseif ($content instanceof Document) {
            return html($this->container['renderer']->render($content));
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
