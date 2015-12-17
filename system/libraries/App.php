<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class App extends Registry {

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
    }

    public function handle($request) {
        $this->request = $request;

        try {
            // send Request Through Router
            //$response = run($request);
            //throw new Exception('not good');
            $response = new Response;
            $response->body = 'hello world';
            
        } catch (Exception $e) {
            $response = $this->renderException($request, $e);
        }

        return $response;
    }

    public function renderException($request, $e) {
        return ExceptionHandler::render($request, $e);
    }

}
