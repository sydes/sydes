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
        $this->response = new Response();
        $this->request = new HttpRequest();
        $this->load = new Loader();

        // загрузить общий конфиг и доступные языки.

        $this->cache = new Cache(DIR_CACHE);

        // определить домен и найти сайт
        // загрузить конфиг сайта
    }
}
