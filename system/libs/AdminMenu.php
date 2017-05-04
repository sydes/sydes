<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

class AdminMenu
{
    private $menu;

    public function __construct($siteId)
    {
        $this->storage = DIR_SITE.'/'.$siteId.'/menu.php';
        $this->menu = file_exists($this->storage) ? include $this->storage : [];
    }

    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param string $path
     * @param array  $data
     * @param int    $weight
     * @return $this
     */
    public function addGroup($path, $data, $weight = 150)
    {
        if (!isset($this->menu[$path])) {
            $this->menu[$path] = [
                'weight' => $weight,
                'title'  => $data['title'],
                'icon'   => ifsetor($data['icon'], 'asterisk'),
                'items'  => [],
            ];
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
        $temp['items'][$item] = array_merge(['url' => '#', 'weight' => $weight], $data);

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

    public function save()
    {
        array2file($this->menu, $this->storage);
    }
}
