<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

use Psr\Http\Message\ResponseInterface;

class Csrf
{
    protected $storage;
    protected $storageLimit;
    protected $strength;
    protected $keyPair;

    /**
     * Create new CSRF guard
     *
     * @param int $strength
     * @param int $storageLimit
     * @throws \RuntimeException if the strength too small
     */
    public function __construct($strength = 16, $storageLimit = 200)
    {
        if ($strength < 16) {
            throw new \RuntimeException('Minimum strength of CSRF token is 16');
        }
        $this->strength = $strength;

        if (!array_key_exists('csrf', $_SESSION)) {
            $_SESSION['csrf'] = [];
        }
        $this->storage = &$_SESSION['csrf'];
        $this->storageLimit = $storageLimit;
        $this->keyPair = null;
    }

    public function check()
    {
        $request = app('request');
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $body = $request->getParsedBody();
            $body = $body ? (array)$body : [];
            $name = ifsetor($body['csrf_name'], false);
            $value = ifsetor($body['csrf_value'], false);
            if (!$name || !$value || !$this->validateToken($name, $value)) {
                $this->generateToken();
                abort(400, t('invalid_csrf_token'));
            }
        }

        $this->generateToken();

        // Enforce the storage limit
        while (count($this->storage) > $this->storageLimit) {
            array_shift($this->storage);
        }
    }

    public function appendHeader(ResponseInterface &$response)
    {
        $request = app('request');
        if (!in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH']) || !$request->isAjax()) {
            return;
        }

        $response = $response
            ->withHeader('x-csrf-name', $this->getTokenName())
            ->withHeader('x-csrf-value', $this->getTokenValue());
    }

    /**
     * Generates a new CSRF token
     *
     * @return array
     */
    public function generateToken()
    {
        $name = uniqid('csrf');
        $value = $this->createToken();
        $this->saveToStorage($name, $value);

        $this->keyPair = [
            'csrf_name' => $name,
            'csrf_value' => $value
        ];
    }

    /**
     * Validate CSRF token from current request
     * against token value stored in $_SESSION
     *
     * @param  string $name  CSRF name
     * @param  string $value CSRF token value
     *
     * @return bool
     */
    public function validateToken($name, $value)
    {
        $token = $this->getFromStorage($name);
        if (function_exists('hash_equals')) {
            $result = ($token !== false && hash_equals($token, $value));
        } else {
            $result = ($token !== false && $token === $value);
        }
        $this->removeFromStorage($name);

        return $result;
    }

    /**
     * @return string
     */
    public function getTokenName()
    {
        return $this->keyPair['csrf_name'];
    }

    /**
     * @return string
     */
    public function getTokenValue()
    {
        return $this->keyPair['csrf_value'];
    }

    public function getField()
    {
        return '<input type="hidden" name="csrf_name" value="'.$this->getTokenName().'">
        <input type="hidden" name="csrf_value" value="'.$this->getTokenValue().'">';
    }

    protected function createToken()
    {
        return bin2hex(openssl_random_pseudo_bytes($this->strength));
    }

    protected function saveToStorage($name, $value)
    {
        $this->storage[$name] = $value;
    }

    protected function getFromStorage($name)
    {
        return ifsetor($this->storage[$name], false);
    }

    protected function removeFromStorage($name)
    {
        unset($this->storage[$name]);
    }
}
