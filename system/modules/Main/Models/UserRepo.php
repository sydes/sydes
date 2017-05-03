<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Main\Models;

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
     * @param array $user
     * @return array
     */
    public function create(array $user)
    {
        $max = count($this->users);

        $user['id'] = $max;
        $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
        $user['autoLogin'] = 0;

        $this->save($user);

        return $user;
    }

    /**
     * @param int   $id
     * @param array $new
     */
    public function update($id, array $new)
    {
        $old = $this->get($id);

        if (isset($new['username'])) {
            $old['username'] = $new['username'];
        }
        if (isset($new['password'])) {
            $old['password'] = password_hash($new['password'], PASSWORD_DEFAULT);
        }
        if (isset($new['autoLogin'])) {
            $old['autoLogin'] = $new['autoLogin'];
        }
        if (isset($new['email'])) {
            $old['email'] = $new['email'];
        }

        $this->save($old);
    }

    /**
     * @param int $id
     * @return array
     */
    public function get($id = 0)
    {
        if (!isset($this->users[$id])) {
            abort(404, 'user_not_found');
        }

        return $this->users[$id];
    }

    /**
     * @param array $user
     */
    protected function save(array $user)
    {
        $this->users[$user['id']] = $user;
        array2file($this->users, $this->storage);
    }
}
