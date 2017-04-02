<?php

namespace PhalApi\Database;

use Medoo\Medoo;
use PhalApi\Contracts\QueryBuilder;

class MedooQueryBuilder implements QueryBuilder
{
    /**
     * @var Medoo
     */
    protected $connection;

    /**
     * @var string 表名
     */
    protected $table;

    /**
     * @var bool 是否开启调试模式
     */
    protected $debug;

    public function __construct($connection, $table, $debug = false)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->debug = $debug;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @param $query
     * @return bool|\PDOStatement
     */
    public function query($query)
    {
        return $this->connection->query($query);
    }

    /**
     * @param $string
     * @return string
     */
    public function quote($string)
    {
        return $this->connection->quote($string);
    }

    /**
     * @param $join
     * @param null $columns
     * @param null $where
     * @return array
     */
    public function select($join, $columns = null, $where = null)
    {
        return $this->connection->select($this->table, $join, $columns, $where);
    }

    /**
     * @param $data
     * @return bool|int
     */
    public function insert($data)
    {
        return $this->connection->insert($this->table, $data);
    }

    /**
     * @param $data
     * @param null $where
     * @return bool|int
     */
    public function update($data, $where = null)
    {
        return $this->connection->update($data, $where);
    }

    /**
     * @param $where
     * @return bool|int
     */
    public function delete($where)
    {
        return $this->connection->delete($this->table, $where);
    }

    /**
     * @param $columns
     * @param null $search
     * @param null $replace
     * @param null $where
     * @return bool|int
     */
    public function replace($columns, $search = null, $replace = null, $where = null)
    {
        return $this->connection->replace($this->table, $columns, $search, $replace, $where);
    }

    /**
     * @param null $join
     * @param null $columns
     * @param null $where
     * @return bool|mixed
     */
    public function get($join = null, $columns = null, $where = null)
    {
        return $this->connection->get($this->table, $join, $columns, $where);
    }

    /**
     * @param $join
     * @param null $where
     * @return bool
     */
    public function has($join, $where = null)
    {
        return $this->connection->has($this->table, $join, $where);
    }

    /**
     * @param null $join
     * @param null $column
     * @param null $where
     * @return bool|int
     */
    public function count($join = null, $column = null, $where = null)
    {
        return $this->connection->count($this->table, $join, $column, $where);
    }

    /**
     * @param $join
     * @param null $column
     * @param null $where
     * @return bool|int|string
     */
    public function max($join, $column = null, $where = null)
    {
        return $this->connection->max($this->table, $join, $column, $where);
    }

    /**
     * @param $join
     * @param null $column
     * @param null $where
     * @return bool|int|string
     */
    public function min($join, $column = null, $where = null)
    {
        return $this->connection->min($this->table, $join, $column, $where);
    }

    /**
     * @param $join
     * @param null $column
     * @param null $where
     * @return bool|int
     */
    public function avg($join, $column = null, $where = null)
    {
        return $this->connection->avg($this->table, $join, $column, $where);
    }

    /**
     * @param $join
     * @param null $column
     * @param null $where
     * @return bool|int
     */
    public function sum($join, $column = null, $where = null)
    {
        return $this->connection->sum($this->table, $join, $column, $where);
    }

    /**
     * @param $actions
     * @return bool
     */
    public function action($actions)
    {
        return $this->connection->action($actions);
    }

    /**
     * @return int|string
     */
    public function id()
    {
        return $this->connection->id();
    }

    /**
     * @return Medoo
     */
    public function debug()
    {
        return $this->connection->debug();
    }

    /**
     * @return array
     */
    public function error()
    {
        return $this->connection->error();
    }

    /**
     * @return mixed
     */
    public function last()
    {
        return $this->connection->last();
    }

    /**
     * @return array
     */
    public function log()
    {
        return $this->connection->log();
    }

    /**
     * @return array
     */
    public function info()
    {
        return $this->connection->info();
    }

    /**
     * @return \PDO
     */
    public function pdo()
    {
        return $this->connection->pdo;
    }

    public function __destruct()
    {
        if ($this->debug) {
            $logs = $this->connection->log();
            if (!empty($logs)) {
                echo "\n\n";
                foreach ($logs as $log) {
                    echo $log . "\n";
                }
                echo "\n\n";
            }
        }
    }
}
