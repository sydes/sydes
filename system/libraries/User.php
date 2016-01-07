<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App;

class User
{
    private $isEditor;
    private $username;
    private $pass;
    private $mastercode;
    private $autologin;

    public function __construct($user)
    {
        foreach ($user as $k => $v) {
            $this->$k = $v;
        }
    }

    public function login($username, $pass, $remember = null)
    {
        if ($username != $this->username || md5($pass) != $this->pass) {
            elog("{$username} is not logged on. {$pass} - wrong password");
            sleep(2);
            return false;
        }

        $_SESSION['hash'] = md5($username.$this->pass.app('request')->ip);
        if (!empty($remember) && $this->autologin == 1) {
            setcookie('hash', $_SESSION['hash'], time() + 604800);
        }
        setcookie('entered', '1', time() + 604800, '/');
        elog($username.' is logged on with a password');
        return true;
    }

    public function logout()
    {
        session_destroy();
        setcookie('hash', '');
    }

    public function isLoggedIn()
    {
        // already logged in
        if (isset($_SESSION['hash'])) {
            if ($_SESSION['hash'] == md5($this->username.$this->pass.app('request')->ip)) {
                return true;
            } else { //hacker or credentials was changed
                $this->logout();
            }
            // login by cookies
        } elseif ($this->autologin == 1 && isset($_COOKIE['hash'])) {
            $hash = md5($this->username.$this->pass.app('request')->ip);
            if ($_COOKIE['hash'] == $hash) {
                $_SESSION['hash'] = $hash;
                elog($this->username.' is logged on with a cookie');
                return true;
            } else {
                $this->logout();
                elog($this->username.' is not logged on. Wrong cookie');
                sleep(2);
            }
        }
        return false;
    }

    public function isEditor()
    {
        if (is_null($this->isEditor)) {
            $this->isEditor = $this->isLoggedIn();
        }
        return $this->isEditor;
    }

    public function isAdmin()
    {
        if (app('request')->has('mastercode') && md5(app('request')->get('mastercode')) == $this->mastercode) {
            $_SESSION['admin'] = 1;
        }
        return isset($_SESSION['admin']);
    }

}
