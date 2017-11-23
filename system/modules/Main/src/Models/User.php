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
        $this->storage = app('dir.storage').'/user.php';
        $this->data = file_exists($this->storage) ? include $this->storage : [];
    }

    /**
     * @param int $id
     * @return Usr
     */
    public function get($id)
    {
        return new Usr($this->data);
    }

    /**
     * @param string $name
     * @return false|Usr
     */
    public function getByName($name)
    {
        if ($this->data['username'] != $name) {
            return false;
        }

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
