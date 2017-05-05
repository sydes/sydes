<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

use Pimple\ServiceProviderInterface;
use Sydes\Settings\Container as Settings;
use Sydes\Settings\FileDriver;
use Zend\Diactoros\ServerRequestFactory;

class DefaultServicesProvider implements ServiceProviderInterface
{
    public function register(\Pimple\Container $container)
    {
        $default['Sydes\Http\Request'] = function () {
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

        $default['Sydes\Cache'] = function () {
            return new Cache(DIR_CACHE);
        };

        $default['renderer'] = function ($c) {
            $class = 'Sydes\Renderer\\'.ucfirst($c['section']);

            return new $class;
        };

        $default['logger'] = function ($c) {
            return new Logger(DIR_LOG.'/'.date('Ym').'.log', $c['request']->getIp());
        };

        $default['app'] = function () {
            $path = DIR_STORAGE.'/app.php';

            return new Settings($path, new FileDriver());
        };

        $default['site'] = function ($c) {
            $path = DIR_SITE.'/'.$c['siteId'].'/config.php';

            return new Settings($path, new FileDriver());
        };

        foreach ($default as $id => $provider) {
            if (!isset($container[$id])) {
                $container[$id] = $provider;
            };
        }
    }
}
