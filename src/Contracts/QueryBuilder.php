<?php

namespace PhalApi\Contracts;

interface QueryBuilder
{
    /**
     * 获取表名
     *
     * @return string
     */
    public function getTable();

    /**
     * 设置表名
     *
     * @param string $table 表名
     * @return void
     */
    public function setTable($table);

    /**
     * @param $join
     * @param null $columns
     * @param null $where
     * @return array
     */
    public function select($join, $columns = null, $where = null);

    /**
     * @param $data
     * @return bool|int
     */
    public function insert($data);

    /**
     * @param $data
     * @param null $where
     * @return bool|int
     */
    public function update($data, $where = null);

    /**
     * @param $where
     * @return bool|int
     */
    public function delete($where);

    /**
     * @param $columns
     * @param null $search
     * @param null $replace
     * @param null $where
     * @return bool|int
     */
    public function replace($columns, $search = null, $replace = null, $where = null);

    /**
     * @param null $join
     * @param null $columns
     * @param null $where
     * @return bool|mixed
     */
    public function get($join = null, $columns = null, $where = null);

    /**
     * @param $join
     * @param null $where
     * @return bool
     */
    public function has($join, $where = null);

    /**
     * @param null $join
     * @param null $column
     * @param null $where
     * @return bool|int
     */
    public function count($join = null, $column = null, $where = null);

    /**
     * @param $join
     * @param null $column
     * @param null $where
     * @return bool|int|string
     */
    public function max($join, $column = null, $where = null);

    /**
     * @param $join
     * @param null $column
     * @param null $where
     * @return bool|int|string
     */
    public function min($join, $column = null, $where = null);

    /**
     * @param $join
     * @param null $column
     * @param null $where
     * @return bool|int
     */
    public function avg($join, $column = null, $where = null);

    /**
     * @param $join
     * @param null $column
     * @param null $where
     * @return bool|int
     */
    public function sum($join, $column = null, $where = null);

    /**
     * @param $actions
     * @return bool
     */
    public function action($actions);

    /**
     * @return int|string
     */
    public function id();

    public function debug();

    /**
     * @return array
     */
    public function error();

    /**
     * @return mixed
     */
    public function last();

    /**
     * @return array
     */
    public function log();

    /**
     * @return array
     */
    public function info();
}
