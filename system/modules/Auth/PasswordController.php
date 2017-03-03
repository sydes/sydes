<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Auth;


class PasswordController
{
    public function sendMail()
    {
        // отправить письмо и заблокировать возможность на час
    }

    public function showResetForm($token)
    {
        // Если токен верный, показать форму для нового пароля
    }

    public function reset()
    {
        // сохранить пароль и залогинить
    }
}
