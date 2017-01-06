<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

class User
{
    private $isEditor;
    public $email;
    public $username;
    public $password;
    public $mastercode;
    public $autologin;

    public function __construct($user)
    {
        foreach ($user as $k => $v) {
            $this->$k = $v;
        }
    }

    public function login($username, $pass, $remember = null)
    {
        if ($username != $this->username || !password_verify($pass, $this->password)) {
            logger("{$username} is not logged on. {$pass} - wrong password");
            sleep(2);
            return false;
        }

        $_SESSION['hash'] = md5($username.$this->password.app('request')->getIp());
        if (!empty($remember) && $this->autologin == 1) {
            setcookie('hash', $_SESSION['hash'], time() + 604800);
        }
        setcookie('entered', '1', time() + 604800, '/');
        logger($username.' is logged on with a password');
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
            if ($_SESSION['hash'] == md5($this->username.$this->password.app('request')->getIp())) {
                return true;
            } else { //hacker or credentials was changed
                $this->logout();
            }
            // login by cookies
        } elseif ($this->autologin == 1 && isset($_COOKIE['hash'])) {
            $hash = md5($this->username.$this->password.app('request')->getIp());
            if ($_COOKIE['hash'] == $hash) {
                $_SESSION['hash'] = $hash;
                logger($this->username.' is logged on with a cookie');
                return true;
            } else {
                $this->logout();
                logger($this->username.' is not logged on. Wrong cookie');
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
        $r = app('request');
        if ($r->has('mastercode') && password_verify($r->get('mastercode'), $this->mastercode)) {
            $_SESSION['admin'] = 1;
        }
        return isset($_SESSION['admin']);
    }
}
