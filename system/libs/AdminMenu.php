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
     */
    public function addGroup($group, $title, $icon = 'asterisk', $weight = 150)
    {
        if (isset($this->menu[$group])) {
            return;
        }

        $this->menu[$group] = [
            'weight' => $weight,
            'title'  => $title,
            'icon'   => $icon,
            'items'  => [],
        ];

        app('site')->set('menu', $this->menu);
    }

    /**
     * @param string $group
     */
    public function removeGroup($group)
    {
        if (isset($this->menu[$group])) {
            unset($this->menu[$group]);

            app('site')->set('menu', $this->menu);
        }
    }

    /**
     * @param string $group
     * @param array  $data ['title' => '', 'url' => '', 'quick_add' => true]
     * @param int    $weight
     */
    public function addItem($group, $data, $weight = 150)
    {
        if (isset($this->menu[$group])) {
            $this->menu[$group]['items'][] = array_merge(['weight' => $weight], $data);

            app('site')->set('menu', $this->menu);
        }
    }

    /**
     * @param string $group
     * @param string $url
     */
    public function removeItem($group, $url)
    {
        if (!isset($this->menu[$group])) {
            return;
        }

        foreach ($this->menu[$group]['items'] as $i => $item) {
            if ($item['url'] == $url) {
                unset($this->menu[$group]['items'][$i]);

                app('site')->set('menu', $this->menu);

                break;
            }
        }
    }

    /**
     * @param string $group
     * @param string $itemUrl
     * @param array  $data ['title' => '', 'url' => '']
     * @param int    $weight
     */
    public function addSubItem($group, $itemUrl, $data, $weight = 150)
    {
        if (!isset($this->menu[$group])) {
            return;
        }

        foreach ($this->menu[$group]['items'] as $i => $item) {
            if ($item['url'] == $itemUrl) {
                $this->menu[$group]['items'][$i]['items'][] = array_merge(['weight' => $weight], $data);

                app('site')->set('menu', $this->menu);

                break;
            }
        }
    }

    /**
     * @param string $group
     * @param string $itemUrl
     * @param string $url
     */
    public function removeSubItem($group, $itemUrl, $url)
    {
        if (!isset($this->menu[$group])) {
            return;
        }

        foreach ($this->menu[$group]['items'] as $i => $item) {
            if ($item['url'] == $itemUrl) {
                foreach ($item['items'] as $j => $subItem) {
                    if ($subItem['url'] == $url) {
                        unset($this->menu[$group]['items'][$i]['items'][$j]);

                        app('site')->set('menu', $this->menu);

                        break 2;
                    }
                }
            }
        }
    }
}
