<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Route\Models;

class Route extends \Sydes\Dao
{
    /**
     * @param string $alias
     * @param string $route
     * @param array  $params
     * @return bool
     */
    public function add($alias, $route, $params = [])
    {
        return $this->db->insert('routes', [
            'alias' => $alias,
            'route' => $route,
            'params' => json_encode($params),
        ]);
    }

    /**
     * @param $alias
     * @return array|bool
     */
    public function findOrFail($alias)
    {
        $route = $this->db->run('SELECT route, params FROM routes WHERE alias = :alias', [
            'alias' => $alias,
        ])->first();

        if ($route) {
            return [$route['route'], json_decode($route['params'], true)];
        }

        return ['Main@error', ['code' => 404]];
    }

    /**
     * @param string $alias
     * @param string $route
     * @param array  $params
     * @return bool
     */
    public function update($alias, $route, $params = [])
    {
        return $this->add($alias, $route, $params);
    }

    /**
     * @param string $alias
     * @return bool
     */
    public function delete($alias)
    {
        return $this->db->delete('routes', [
            'alias' => $alias,
        ]);
    }
}
