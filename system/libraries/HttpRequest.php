<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class HttpRequest {

    /**
     * Query string parameters ($_GET)
     *
     * @var array
     */
    public $query;

    /**
     * Request body parameters ($_POST)
     *
     * @var array
     */
    public $request;

    /**
     * Cookies ($_COOKIE)
     *
     * @var array
     */
    public $cookie;

    /**
     * Uploaded files ($_FILES)
     *
     * @var array
     */
    public $files;

    /**
     * Server and execution environment parameters ($_SERVER)
     *
     * @var array
     */
    public $server;

    /**
     * Headers (taken from the $_SERVER).
     *
     * @var array
     */
    public $headers;

    /**
     * List of languages acceptable by the client browser
     *
     * @var string
     */
    public $languages;

    /**
     * Gets the request's scheme.
     *
     * @var string
     */
    public $scheme = 'http';

    /**
     * Gets client ip.
     *
     * @var string
     */
    public $ip;

    /**
     * Was the request made by POST?.
     *
     * @var bool
     */
    public $is_post = false;

    /**
     * Returns true if the request is a XMLHttpRequest.
     *
     * @var bool
     */
    public $is_ajax = false;

    /**
     * Checks whether the request is secure or not.
     *
     * @var bool
     */
    public $is_secure = false;

    public function __construct() {
        if (!isset($_SERVER['HTTP_HOST'])) {
            header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request');
            trigger_error('', E_USER_ERROR);
        }

        session_start();

        $this->query = $_GET;
        $this->request = $_POST;
        $this->cookie = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->headers = $this->getHeaders($_SERVER);

        $this->ip = $_SERVER['REMOTE_ADDR'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->is_post = true;
        }

        if (isset($this->headers['X_REQUESTED_WITH']) &&
                strtolower($this->headers['X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->is_ajax = true;
        }

        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
                (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
            $this->scheme = 'https';
            $this->is_secure = true;
        }
    }

    /**
     * Gets a "parameter" value from request.
     *
     * @param string $key     the key
     * @param mixed  $default the default value
     * @return mixed
     */
    public function get($key, $default = null) {
        if (isset($this->query[$key])) {
            return $this->query[$key];
        } elseif (isset($this->request[$key])) {
            return $this->request[$key];
        } else {
            return $default;
        }
    }

    /**
     * Determine if the request contains a non-empty value for an input item.
     *
     * @param string|array $key
     * @return bool
     */
    public function has($key) {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $value) {
            if ($this->isEmptyString($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the given input key is an empty string for "has".
     *
     * @param  string $key
     * @return bool
     */
    protected function isEmptyString($key) {
        $value = $this->get($key);
        $boolOrArray = is_bool($value) || is_array($value);
        return !$boolOrArray && trim((string) $value) === '';
    }

    /**
     * Gets a required "parameter" value from request or throws Exception.
     *
     * @param string $key     the key
     * @param mixed  $default the default value
     * @return mixed
     * @throws BaseException
     */
    public function getRequired($key) {
        $value = $this->get($key);
        if (is_null($value) || ($value === '')) {
            throw new BaseException(sprintf(t('error_parameter_required'), $key));
        }
    }

    /**
     * Get a subset of the items from the input data.
     *
     * @param  array|mixed $keys
     * @return array
     */
    public function only($keys) {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = array();
        $input = $this->all();
        foreach ($keys as $key) {
            $results[$key] = isset($input[$key]) ? $input[$key] : null;
        }
        return $results;
    }

    /**
     * Get all of the input for the request.
     *
     * @return array
     */
    public function all() {
        return array_replace_recursive($this->request, $this->query);
    }

    /**
     * Gets the HTTP headers.
     *
     * @return array
     */
    private function getHeaders($server) {
        $headers = array();
        $contentHeaders = array('CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true);
        foreach ($server as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } elseif (isset($contentHeaders[$key])) {
                $headers[$key] = $value;
            }
        }
        return $headers;
    }

    /**
     * Gets a list of languages acceptable by the client browser.
     *
     * @return array Languages ordered in the user browser preferences
     */
    public function getLanguages() {
        if (!is_null($this->languages)) {
            return $this->languages;
        }

        if (preg_match_all('/([a-z*]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $list)) {
            $langs = array_combine($list[1], $list[2]);
            foreach ($langs as $lang => $weight) {
                if ($lang == '*') {
                    continue;
                }
                if (false !== strpos($lang, '-')) {
                    $codes = explode('-', $lang);
                    for ($i = 0, $max = count($codes); $i < $max; ++$i) {
                        if ($i === 0) {
                            $lang = strtolower($codes[0]);
                        } else {
                            $lang .= '_'.strtoupper($codes[$i]);
                        }
                    }
                }
                $languages[$lang] = $weight ? $weight : 1;
            }
            arsort($languages, SORT_NUMERIC);
            $this->languages = array_keys($languages);
        }
        return $this->languages;
    }

    /**
     * Returns the preferred language.
     *
     * @param array $locales An array of ordered available locales
     * @return string|null The preferred locale
     */
    public function getPreferredLanguage(array $locales = null) {
        $prefLangs = $this->getLanguages();
        if (empty($locales)) {
            return isset($prefLangs[0]) ? $prefLangs[0] : null;
        }
        if (!$prefLangs) {
            return $locales[0];
        }
        $extPrefLangs = array();
        foreach ($prefLangs as $language) {
            $extPrefLangs[] = $language;
            if (false !== $position = strpos($language, '_')) {
                $superLanguage = substr($language, 0, $position);
                if (!in_array($superLanguage, $prefLangs)) {
                    $extPrefLangs[] = $superLanguage;
                }
            }
        }
        $prefLangs = array_values(array_intersect($extPrefLangs, $locales));
        return isset($prefLangs[0]) ? $prefLangs[0] : $locales[0];
    }

}
