<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Route\Models;

use Sydes\Database\Connection;
use Sydes\Database\Schema\Blueprint;

class Route
{
    protected $db;

    public function __construct(Connection $db) {
        $this->db = $db;
    }

    /**
     * @param string $alias
     * @param string $route
     * @param array  $params
     * @return bool
     */
    public function add($alias, $route, $params = [])
    {
        $data = [
            'alias' => $alias,
            'route' => $route,
            'params' => json_encode($params),
        ];

        return $this->db->table('routes')->insert($data);
    }

    /**
     * @param $alias
     * @return array|bool
     */
    public function find($alias)
    {
        $route = $this->db->table('routes')->where('alias', $alias)->first();

        if ($route) {
            return [$route->route, json_decode($route->params, true)];
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
        return $this->db->table('routes')->updateOrInsert([
            'alias' => $alias,
        ], [
            'route' => $route,
            'params' => json_encode($params),
        ]);
    }

    /**
     * @param string $alias
     * @return bool
     */
    public function delete($alias)
    {
        return $this->db->table('routes')->where('alias', $alias)->delete();
    }

    public function make()
    {
        $this->db->getSchemaBuilder()->create('routes', function (Blueprint $t){
            $t->string('alias');
            $t->string('route');
            $t->string('params');

            $t->primary('alias');
        });
    }

    public function drop()
    {
        $this->db->getSchemaBuilder()->drop('routes');
    }
}
