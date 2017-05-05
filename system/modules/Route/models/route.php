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
        $stmt = $this->db->prepare("INSERT OR REPLACE INTO routes VALUES (?, ?, ?)");

        return $stmt->execute([
            $alias,
            $route,
            json_encode($params),
        ]);
    }

    /**
     * @param $alias
     * @return array|bool
     */
    public function findOrFail($alias)
    {
        $stmt = $this->db->prepare("SELECT route, params FROM routes WHERE alias = ?");
        $stmt->execute([$alias]);
        if (!$route = $stmt->fetch()) {
            abort(404, t('page_not_found'));
        }

        return [$route['route'], json_decode($route['params'], true)];
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
        $stmt = $this->db->prepare("DELETE FROM routes WHERE alias = ?");

        return $stmt->execute([$alias]);
    }
}
