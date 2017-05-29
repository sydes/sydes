<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Sydes;

class PDO extends \PDO
{
    /** @var \PDOStatement */
    protected $stmt;

    /**
     * @param string $sql
     * @param array  $data
     * @return self|\PDOStatement
     */
    public function run($sql, array $data = [])
    {
        if (!$data) {
            return $this->query($sql);
        }

        $stmt = $this->prepare($sql);
        $this->bind($stmt, $data);
        $stmt->execute();

        $this->stmt = $stmt;

        return $this;
    }


    public function select($table, array $cols = ['*'], array $wheres = [], array $opts = [])
    {
        list($where, $data) = $this->makeWhere($wheres);

        $sql = 'SELECT '.implode(', ', $cols).' FROM '.$table.' WHERE '.$where;

        $stmt = $this->prepare($sql);
        $this->bind($stmt, $data);
        $stmt->execute();

        $this->stmt = $stmt;

        return $this;
    }

    protected function makeWhere($wheres)
    {
        $data = $sql = [];
        if (!is_array($wheres[0])) {
            $wheres = [$wheres];
        }

        foreach ($wheres as $where) {
            if (count($where) == 1) {
                $sql[] = "{$where[0]} IS NOT NULL";
            } elseif (count($where) == 2) {
                if (strpos($where[1], '*') !== false) {
                    $sql[] = "{$where[0]} LIKE :{$where[0]}";
                    $data[$where[0]] = str_replace('*', '%', $where[1]);
                } else {
                    $sql[] = "{$where[0]} = :{$where[0]}";
                    $data[$where[0]] = $where[1];
                }
            } else {
                $where[1] = strtolower($where[1]);
                if ($where[1] == 'between') {
                    $sql[] = "{$where[0]} BETWEEN :{$where[0]}1 AND :{$where[0]}2";
                    if (count($where) == 3 && is_array($where[2])) {
                        $data[$where[0].'1'] = (int)$where[2][0];
                        $data[$where[0].'2'] = (int)$where[2][1];
                    } else {
                        $data[$where[0].'1'] = (int)$where[2];
                        $data[$where[0].'2'] = (int)$where[3];
                    }
                } elseif ($where[1] == 'in') {
                    if (is_string($where[2])) {
                        $where[2] = explode(',', $where[2]);
                    }

                    foreach ($where[2] as $i => $val) {
                        $values[] = ':'.$where[0].$i;
                        $data[$where[0].$i] = (int)$val;
                    }
                    $values = implode(',', $values);

                    $sql[] = "{$where[0]} IN ({$values})";
                } else {
                    if (!in_array(strtolower($where[1]), ['>', '>=', '<', '<=', '=', '!=', 'LIKE'])) {
                        throw new \Exception('Unknown where criterion '.$where[1]);
                    }

                    if ($where[1] == 'LIKE') {
                        $where[2] = str_replace('*', '%', $where[2]);
                    }
                    $sql[] = "{$where[0]} {$where[1]} :{$where[0]}";
                    $data[$where[0]] = $where[2];
                }
            }
        }

        $sql = implode(' AND ', $sql);

        return [$sql, $data];
    }

    /**
     * @param  string $table name
     * @param  array  $data  named params
     * @return string
     */
    public function insert($table, array $data)
    {
        $keys = array_keys($data);
        $names = implode(', ', $keys);
        $values = ':'.implode(', :', $keys);

        $stmt = $this->prepare("INSERT OR REPLACE INTO $table ($names) VALUES ($values)");
        $this->bind($stmt, $data);
        $stmt->execute();

        return $this->lastInsertId();
    }

    /**
     * @param  string $table name
     * @param  array  $data  named params
     * @param  array  $where conditions
     * @return int
     */
    public function update($table, array $data, array $where = [])
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key,";
        }
        $fields = implode(', ', $fields);

        $wheres = [];
        foreach ($where as $key => $value) {
            $wheres[] = "$key = :$key";
        }
        $wheres = implode(' AND ', $wheres);

        $stmt = $this->prepare("UPDATE $table SET $fields WHERE $wheres");
        $this->bind($stmt, $data);
        $this->bind($stmt, $where);

        return $stmt->execute() ? $stmt->rowCount() : 0;
    }

    /**
     * Delete method
     *
     * @param  string $table name
     * @param  array  $where conditions
     * @param  int    $limit number of records
     * @return int
     */
    public function delete($table, array $where, $limit = 0)
    {
        $wheres = [];
        foreach ($where as $key => $value) {
            $wheres[] = "$key = :$key";
        }
        $wheres = implode(' AND ', $wheres);

        $limit = $limit ? "LIMIT $limit" : '';

        $stmt = $this->prepare("DELETE FROM $table WHERE $wheres $limit");
        $this->bind($stmt, $where);

        return $stmt->execute() ? $stmt->rowCount() : 0;
    }

    /**
     * @param  string $table name
     * @return int
     */
    public function truncate($table)
    {
        $this->exec("DELETE FROM $table");

        return $this->exec("VACUUM");
    }

    /**
     * @param  string $table name
     * @return int
     */
    public function drop($table)
    {
        $this->exec("DROP TABLE IF EXISTS $table");

        return $this->exec("VACUUM");
    }

    /**
     * @return mixed
     */
    public function fetch()
    {
        return $this->stmt->fetch();
    }

    /**
     * @param int $colNum
     * @return string
     */
    public function fetchColumn($colNum = 0)
    {
        return $this->stmt->fetchColumn($colNum);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->stmt->fetchAll();
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return $this->stmt->fetch();
    }

    /**
     * @param int $colNum
     * @return array
     */
    public function column($colNum = 0)
    {
        return $this->stmt->fetchAll(PDO::FETCH_COLUMN, $colNum);
    }

    /**
     * @return array
     */
    public function pairs()
    {
        return $this->stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * @return array
     */
    public function unique()
    {
        return $this->stmt->fetchAll(PDO::FETCH_UNIQUE);
    }

    /**
     * @param \PDOStatement $stmt
     * @param array         $data
     */
    protected function bind(\PDOStatement $stmt, array $data)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
                $stmt->bindValue(":$key", $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":$key", $value);
            }
        }
    }
}
