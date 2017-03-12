<?php
use App\Model;

class RouteModel extends Model
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
        if ($route = $stmt->fetch()) {
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
        $stmt = $this->db->prepare("DELETE FROM routes WHERE alias = ?");

        return $stmt->execute([$alias]);
    }
}
