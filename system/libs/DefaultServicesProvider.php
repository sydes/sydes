<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

use Pimple\ServiceProviderInterface;
use Sydes\L10n\Translator;
use Sydes\Settings\Container as Settings;
use Sydes\Settings\FileDriver;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

class DefaultServicesProvider implements ServiceProviderInterface
{
    public function register(\Pimple\Container $container)
    {
        $default['emitter'] = function () {
            return new SapiEmitter();
        };

        $default['request'] = function () {
            $r = ServerRequestFactory::fromGlobals();

            return new Http\Request(
                $r->getServerParams(),
                $r->getUploadedFiles(),
                $r->getUri(),
                $r->getMethod(),
                $r->getBody(),
                $r->getHeaders(),
                $r->getCookieParams(),
                $r->getQueryParams(),
                $r->getParsedBody(),
                $r->getProtocolVersion()
            );
        };

        $default['router'] = function () {
            return new Router();
        };

        $default['cache'] = function () {
            return new Cache(DIR_CACHE);
        };

        $default['translator'] = function () {
            return new Translator();
        };

        $default['event'] = function () {
            return new Event();
        };

        $default['csrf'] = function () {
            return new Csrf();
        };

        $default['db'] = function ($c) {
            return new Database($c['siteId']);
        };

        $default['renderer'] = function ($c) {
            $class = 'Sydes\Renderer\\'.ucfirst($c['section']);

            return new $class;
        };

        $default['logger'] = function ($c) {
            return new Logger(DIR_LOG.'/'.date('Ym').'.log', $c['request']->getIp());
        };

        $default['api'] = function () {
            return new Api();
        };

        $default['app'] = function () {
            $path = DIR_STORAGE.'/app.php';

            return new Settings($path, new FileDriver());
        };

        $default['site'] = function ($c) {
            $path = DIR_SITE.'/'.$c['siteId'].'/config.php';

            return new Settings($path, new FileDriver());
        };

        $default['adminMenu'] = function ($c) {
            return new AdminMenu($c['site']);
        };

        foreach ($default as $id => $provider) {
            if (!isset($container[$id])) {
                $container[$id] = $provider;
            };
        }
    }
}
