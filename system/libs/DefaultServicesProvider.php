<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

use Sydes\L10n\Translator;
use Sydes\Renderer\Base;
use Sydes\Settings\Container as Settings;
use Sydes\Settings\FileDriver;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

/**
 * Default Service Provider.
 */
class DefaultServicesProvider
{
    /**
     * Register default services.
     *
     * @param Container $c A DI container implementing ArrayAccess.
     */
    public function register($c)
    {
        if (!isset($c['emitter'])) {
            /**
             * @return SapiEmitter
             */
            $c['emitter'] = function () {
                return new SapiEmitter();
            };
        };

        if (!isset($c['request'])) {
            /**
             * @return ServerRequestInterface
             */
            $c['request'] = function () {
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
        };

        if (!isset($c['router'])) {
            /**
             * @return Router
             */
            $c['router'] = function () {
                return new Router();
            };
        };

        if (!isset($c['cache'])) {
            /**
             * @return Cache
             */
            $c['cache'] = function () {
                return new Cache(DIR_CACHE);
            };
        };

        if (!isset($c['translator'])) {
            /**
             * @return Translator
             */
            $c['translator'] = function () {
                return new Translator();
            };
        };

        if (!isset($c['event'])) {
            /**
             * @return Event
             */
            $c['event'] = function () {
                return new Event();
            };
        };

        if (!isset($c['csrf'])) {
            /**
             * @return Csrf
             */
            $c['csrf'] = function () {
                return new Csrf();
            };
        };

        if (!isset($c['db'])) {
            /**
             * @param $c
             * @return Database
             */
            $c['db'] = function ($c) {
                return new Database($c['siteId']);
            };
        };

        if (!isset($c['renderer'])) {
            /**
             * @param $c
             * @return Base
             */
            $c['renderer'] = function ($c) {
                $class = 'Sydes\Renderer\\'.ucfirst($c['section']);
                return new $class;
            };
        };

        if (!isset($c['user'])) {
            /**
             * @return User
             */
            $c['user'] = function () {
                $data = include DIR_APP.'/user.php';

                return new User($data);
            };
        };

        if (!isset($c['logger'])) {
            /**
             * @param $c
             * @return Logger
             */
            $c['logger'] = function ($c) {
                return new Logger(DIR_LOG.'/'.date('Ym').'.log', $c['request']->getIp());
            };
        };

        /**
         * @return Api
         */
        $c['api'] = function () {
            return new Api();
        };

        /**
         * @return Cmf
         */
        $c['cmf'] = function () {
            return new Cmf();
        };

        $c['app'] = function () {
            $path = DIR_APP.'/config.php';

            return new Settings($path, new FileDriver());
        };

        $c['site'] = function ($c) {
            $path = DIR_SITE.'/'.$c['siteId'].'/config.php';

            return new Settings($path, new FileDriver());
        };

        $c['adminMenu'] = function ($c) {
            return new AdminMenu($c['site']->get('menu'));
        };
    }
}
