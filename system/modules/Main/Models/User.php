<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Main\Models;

use Sydes\User as Usr;

class User
{
    protected $storage;
    protected $data;

    public function __construct()
    {
        $this->storage = DIR_STORAGE.'/user.php';
        $this->data = file_exists($this->storage) ? include $this->storage : [];
    }

    /**
     * @return Usr
     */
    public function get()
    {
        return new Usr($this->data);
    }

    /**
     * @param Usr $user
     */
    public function save(Usr $user)
    {
        $this->data = $user->toArray();
        array2file($this->data, $this->storage);
    }
}
