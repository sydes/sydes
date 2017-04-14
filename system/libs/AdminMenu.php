<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

class AdminMenu
{
    private $menu;

    public function __construct($menu)
    {
        $this->menu = $menu;
    }

    /**
     * @param string $group
     * @param string $title
     * @param string $icon
     * @param int    $weight
     * @return $this
     */
    public function addGroup($group, $title, $icon = 'asterisk', $weight = 150)
    {
        if (!isset($this->menu[$group])) {
            $this->menu[$group] = [
                'weight' => $weight,
                'title'  => $title,
                'icon'   => $icon,
                'items'  => [],
            ];

            app('site')->set('menu', $this->menu)->save();
        }

        return $this;
    }

    /**
     * @param string $group
     * @return $this
     */
    public function removeGroup($group)
    {
        if (isset($this->menu[$group])) {
            unset($this->menu[$group]);

            app('site')->set('menu', $this->menu)->save();
        }

        return $this;
    }

    /**
     * @param string $path group/item/subItem
     * @param array  $data ['title' => '', 'url' => '', 'quick_add' => true]
     * @param int    $weight
     * @return $this
     */
    public function addItem($path, $data, $weight = 150)
    {
        $path = explode('/', $path);
        $item = array_pop($path);

        $temp = &$this->selectBy($path);
        $temp['items'][$item] = array_merge(['weight' => $weight], $data);

        app('site')->set('menu', $this->menu)->save();

        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function removeItem($path)
    {
        $path = explode('/', $path);
        $item = array_pop($path);

        $temp = &$this->selectBy($path);
        unset($temp['items'][$item]);

        app('site')->set('menu', $this->menu)->save();

        return $this;
    }

    private function &selectBy($parts)
    {
        $group = array_shift($parts);

        if (!isset($this->menu[$group])) {
            throw new \OutOfBoundsException('Menu group "'.$group.'" not found');
        }

        $temp = &$this->menu[$group];
        foreach ($parts as $part) {
            if (!isset($temp['items'][$part])) {
                throw new \OutOfBoundsException('Path "'.implode('/', $parts).'" not found in menu');
            }

            $temp = &$temp['items'][$part];
        }

        return $temp;
    }
}
