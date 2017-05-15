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
     * @return self
     */
    public function run($sql, array $data = null)
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

    /**
     * @param  string $sql  sql query
     * @param  array  $data named params
     * @return self
     */
    public function select($sql, array $data = [])
    {
        if (strtolower(substr($sql, 0, 7)) !== 'select ') {
            $sql = 'SELECT '.$sql;
        }

        $stmt = $this->prepare($sql);
        $this->bind($stmt, $data);
        $stmt->execute();

        $this->stmt = $stmt;

        return $this;
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
        return $this->exec("TRUNCATE TABLE $table");
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
