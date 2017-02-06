<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

use App\Http\ServerRequestFactory;
use App\L10n\Translator;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\SapiEmitter;

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
        if (!isset($c['exceptionHandler'])) {
            /**
             * @param $c
             * @return Exception\BaseHandler|Exception\SiteHandler
             */
            $c['exceptionHandler'] = function ($c) {
                $place = $c['section'] == 'base' ? 'Base' : 'Site';
                $class = 'App\Exception\\'.$place.'Handler';
                return new $class;
            };
        };

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
                return ServerRequestFactory::fromGlobals();
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
                return new Database($c['site']['id']);
            };
        };

        if (!isset($c['renderer'])) {
            /**
             * @param $c
             */
            $c['renderer'] = function ($c) {
                $class = 'App\Renderer\\'.ucfirst($c['section']);
                return new $class;
            };
        };

        if (!isset($c['theme'])) {
            /**
             * @param $c
             * @return Theme
             */
            $c['theme'] = function ($c) {
                return new Theme($c['site']['theme']);
            };
        };

        if (!isset($c['editor'])) {
            /**
             * @param $c
             * @return Editor
             */
            $c['editor'] = function ($c) {
                return new Editor($c['rawAppConfig']['user']);
            };
        };

        if (!isset($c['logger'])) {
            /**
             * @return Logger
             */
            $c['logger'] = function () {
                return new Logger(DIR_LOG.'/'.date('Ym').'.log');
            };
        };

        /**
         * @return Api
         */
        $c['api'] = function () {
            return new Api();
        };
    }
}
