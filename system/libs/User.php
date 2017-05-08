<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

class User
{
    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param array $data username, password, email, autoLogin
     * @return User
     */
    public static function create(array $data)
    {
        if (!isset($data['email'])) {
            $data['email'] = '';
        }
        if (!isset($data['autoLogin'])) {
            $data['autoLogin'] = 0;
        }

        $u = new self($data);
        $u->setPassword($data['password']);

        return $u;
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function set($key, $value)
    {
        if ($key == 'password') {
            $this->setPassword($value);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        return $this->data[$key];
    }

    /**
     * @param string $pass
     */
    public function setPassword($pass)
    {
        $this->data['password'] = password_hash($pass, PASSWORD_DEFAULT);
    }

    /**
     * @param string $pass
     * @return bool
     */
    public function checkPassword($pass)
    {
        return password_verify($pass, $this->data['password']);
    }

    public function toArray()
    {
        return $this->data;
    }
}
