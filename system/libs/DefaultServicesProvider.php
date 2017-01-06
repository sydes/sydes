<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\SapiEmitter;
use App\Http\ServerRequestFactory;

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
        /**
         * @param $c
         * @return Exception\BaseHandler|Exception\SiteHandler
         */
        $c['exceptionHandler'] = function ($c) {
            return isset($c['site']) ? new Exception\SiteHandler : new Exception\BaseHandler;
        };

        if (!isset($c['emitter'])) {
            /**
             * Basic emitter for response
             *
             * @return SapiEmitter
             */
            $c['emitter'] = function () {
                return new SapiEmitter;
            };
        };

        if (!isset($c['request'])) {
            /**
             * PSR-7 Request object
             *
             * @return ServerRequestInterface
             */
            $c['request'] = function () {
                return ServerRequestFactory::fromGlobals();
            };
        };

        if (!isset($c['router'])) {
            /**
             * fast-router bridge
             *
             * @return Router
             */
            $c['router'] = function () {
                return new Router;
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
                return new Translator;
            };
        };

        if (!isset($c['event'])) {
            /**
             * @return Event
             */
            $c['event'] = function () {
                return new Event;
            };
        };

        if (!isset($c['csrf'])) {
            /**
             * @return Csrf
             */
            $c['csrf'] = function () {
                return new Csrf;
            };
        };

        /**
         * @param $c
         * @return Database
         */
        $c['db'] = function ($c) {
            return new Database($c['site']['id']);
        };

        /**
         * @param $c
         */
        $c['renderer'] = function ($c) {
            $class = 'App\Renderer\\'.ucfirst($c['section']);
            return new $class;
        };

        /**
         * @param $c
         * @return Theme
         */
        $c['theme'] = function ($c) {
            return new Theme($c['site']['theme']);
        };

        /**
         * @param $c
         * @return User
         */
        $c['user'] = function ($c) {
            return new User($c['rawAppConfig']['user']);
        };
    }
}
