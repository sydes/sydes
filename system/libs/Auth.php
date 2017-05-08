<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

class Auth
{
    /** @var User */
    protected $user;
    protected $logged;

    public function __construct()
    {
        $this->user = model('Main/User')->get();
    }

    /**
     * @param string $username
     * @param string $pass
     * @param bool   $remember
     * @return bool
     */
    public function attempt($username, $pass, $remember = false)
    {
        if ($username != $this->user->get('username') || !$this->user->checkPassword($pass)) {
            return false;
        }

        return $this->login($remember);
    }

    /**
     * @param bool $remember
     * @return bool
     */
    public function login($remember = false)
    {
        $_SESSION['hash'] = $this->hash();
        setcookie('entered', '1', time() + 604800, '/');

        if ($remember && $this->user->get('autoLogin') == 1) {
            setcookie('hash', $_SESSION['hash'], time() + 604800);
        }

        return true;
    }

    public function logout()
    {
        session_destroy();
        setcookie('hash', '', 1, '/');
        setcookie('entered', '', 1, '/');
    }

    private function checkLogin()
    {
        $hash = $this->hash();

        if (isset($_SESSION['hash'])) { // already logged in
            if ($_SESSION['hash'] == $hash) {
                return true;
            } else {
                $this->logout();
            }
        } elseif ($this->user->get('autoLogin') == 1 && isset($_COOKIE['hash'])) { // login by cookies
            if ($_COOKIE['hash'] == $hash) {
                return $this->login(true);
            } else {
                $this->logout();
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function check()
    {
        if (is_null($this->logged)) {
            $this->logged = $this->checkLogin();
        }

        return $this->logged;
    }

    /**
     * @param string $key
     * @return string|User
     */
    public function getUser($key = null)
    {
        return $key === null ? $this->user : $this->user->get($key);
    }

    protected function hash()
    {
        return md5($this->user->get('username').$this->user->get('password').app('request')->getIp());
    }
}
