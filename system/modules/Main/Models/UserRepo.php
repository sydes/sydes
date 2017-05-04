<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Main\Models;

use Sydes\User;

class UserRepo
{
    protected $storage;
    protected $users;

    public function __construct()
    {
        $this->storage = DIR_STORAGE.'/users.php';
        $this->users = file_exists($this->storage) ? include $this->storage : [];
    }

    /**
     * @param User $user
     */
    public function create(User $user)
    {
        $user->set('id', count($this->users));
        $this->save($user);
    }

    /**
     * @param int $id
     * @return bool|User
     */
    public function get($id = 0)
    {
        if (!isset($this->users[$id])) {
            return null;
        }

        return new User($this->users[$id]);
    }

    /**
     * @param User $user
     */
    public function save(User $user)
    {
        $this->users[$user->get('id')] = $user->toArray();
        array2file($this->users, $this->storage);
    }
}
