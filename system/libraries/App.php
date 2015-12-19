<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class App extends Pimple\Container {

    private static $instance;

    private function __clone() {}

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialization of application
     *
     */
    public function init() {
        $this->load = new Loader();

        // load main config and languages

        $this->cache = new Cache(DIR_CACHE);

        // find site by domain
        // load site config
        
        $this['config'] = include DIR_APP.'/config.php';

        date_default_timezone_set($this['config']['app']['time_zone']);
        mb_internal_encoding('UTF-8');
    }

    public function run() {
        // send Request Through Router
        //$response = run($request);
        //throw new BaseException('not good');
        //strpos();
        $response = new Response;
        $response->body = 'hello world '. print_r($response, true);

        return $response;
    }

}
