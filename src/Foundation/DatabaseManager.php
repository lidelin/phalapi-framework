<?php

namespace PhalApi\Foundation;

use Medoo\Medoo;
use Exception;
use PhalApi\Exceptions\InternalServerError;
use PhalApi\Contracts\QueryBuilder;
use PhalApi\Database\MedooQueryBuilder;

class DatabaseManager
{
    /**
     * @var array 数据库链接池
     */
    protected $connections = [];

    /**
     * @var QueryBuilder 查询构造器实例池
     */
    protected $queryBuilders = [];

    /**
     * @var array $configs 数据库配置
     */
    protected $configs = [];

    /**
     * @var bool $debug 是否开启调试模式，调试模式下会输出全部执行的SQL语句和对应消耗的时间
     */
    protected $debug = false;

    /**
     * Database constructor.
     *
     * @param array $configs 数据库配置
     * @param bool $debug 是否开启调试模式
     */
    public function __construct($configs, $debug = false)
    {
        $this->configs = $configs;
        $this->debug = $debug;
    }

    /**
     * @param $name
     * @return QueryBuilder
     */
    public function __get($name)
    {
        $queryBuilderKey = $this->createQueryBuilderKey($name);

        if (!isset($this->queryBuilders[$queryBuilderKey])) {
            list($tableName, $suffix) = $this->parseName($name);
            $router = $this->getDBRouter($tableName, $suffix);

            $this->queryBuilders[$queryBuilderKey] = new MedooQueryBuilder($router['connection'], $name, $this->debug);
        }

        return $this->queryBuilders[$queryBuilderKey];
    }

    public function __set($name, $value)
    {
        foreach ($this->queryBuilders as $key => $queryBuilder) {
            $queryBuilder->$name = $value;
        }
    }

    protected function createQueryBuilderKey($tableName)
    {
        return '__' . $tableName . '__';
    }

    /**
     * 解析分布式表名
     * 表名  + ['_' + 数字后缀]，如：user_0, user_1, ... user_100
     *
     * @param string $name
     * @return array
     */
    protected function parseName($name)
    {
        $tableName = $name;
        $suffix = null;

        $pos = strrpos($name, '_');
        if ($pos !== false) {
            $tableId = substr($name, $pos + 1);
            if (is_numeric($tableId)) {
                $tableName = substr($name, 0, $pos);
                $suffix = intval($tableId);
            }
        }

        return [$tableName, $suffix];
    }

    /**
     * 获取分布式数据库路由
     *
     * @param string $tableName 数据库表名
     * @param string $suffix 分布式下的表后缀
     * @throws InternalServerError
     * @return array 数据库配置
     */
    protected function getDBRouter($tableName, $suffix)
    {
        $rs = ['prefix' => '', 'key' => '', 'connection' => null, 'isNoSuffix' => false];

        $defaultMap = !empty($this->configs['tables']['__default__'])
            ? $this->configs['tables']['__default__'] : [];
        $tableMap = !empty($this->configs['tables'][$tableName])
            ? $this->configs['tables'][$tableName] : $defaultMap;

        if (empty($tableMap)) {
            throw new InternalServerError(
                T('No table map config for {tableName}', ['tableName' => $tableName])
            );
        }

        $dbKey = null;
        $dbDefaultKey = null;
        if (!isset($tableMap['map'])) {
            $tableMap['map'] = [];
        }
        foreach ($tableMap['map'] as $map) {
            $isMatch = false;

            if ((isset($map['start']) && isset($map['end']))) {
                if ($suffix !== null && $suffix >= $map['start'] && $suffix <= $map['end']) {
                    $isMatch = true;
                }
            } else {
                $dbDefaultKey = $map['db'];
                if ($suffix === null) {
                    $isMatch = true;
                }
            }

            if ($isMatch) {
                $dbKey = isset($map['db']) ? trim($map['db']) : null;
                break;
            }
        }
        //try to use default map if no perfect match
        if ($dbKey === null) {
            $dbKey = $dbDefaultKey;
            $rs['isNoSuffix'] = true;
        }

        if ($dbKey === null) {
            throw new InternalServerError(
                T('No db router match for {tableName}', ['tableName' => $tableName])
            );
        }

        $rs['connection'] = $this->getConnection($dbKey);
        $rs['prefix'] = isset($tableMap['prefix']) ? trim($tableMap['prefix']) : '';
        $rs['key'] = isset($tableMap['key']) ? trim($tableMap['key']) : 'id';

        return $rs;
    }

    /**
     * 创建数据库连接，如果需要采用其他数据库，可重载此函数
     *
     * @param string $dbKey 数据库表名唯一KEY
     * @throws InternalServerError
     * @return Medoo
     */
    protected function getConnection($dbKey)
    {
        if (!isset($this->connections[$dbKey])) {
            $dbCfg = isset($this->configs['servers'][$dbKey])
                ? $this->configs['servers'][$dbKey] : [];

            if (empty($dbCfg)) {
                throw new InternalServerError(
                    T('no such db:{db} in servers', ['db' => $dbKey]));
            }

            try {
                $this->connections[$dbKey] = new Medoo([
                    'database_type' => 'mysql',
                    'database_name' => $dbCfg['name'],
                    'server' => $dbCfg['host'],
                    'username' => $dbCfg['user'],
                    'password' => $dbCfg['password'],
                    'charset' => $dbCfg['charset'],
                    'port' => $dbCfg['port'],
                    'prefix' => $dbCfg['prefix'],
                ]);
            } catch (Exception $ex) {
                //异常时，接口异常返回，并隐藏数据库帐号信息
                $errorMsg = T('can not connect to database: {db}', ['db' => $dbKey]);
                if (DI()->debug) {
                    $errorMsg = T('can not connect to database: {db}, code: {code}, cause: {msg}',
                        ['db' => $dbKey, 'code' => $ex->getCode(), 'msg' => $ex->getMessage()]);
                }
                throw new InternalServerError($errorMsg);
            }
        }

        return $this->connections[$dbKey];
    }

    /**
     * 断开数据库链接
     */
    public function disconnect()
    {
        foreach ($this->connections as $dbKey => $connection) {
            $this->connections[$dbKey] = null;
            unset($this->connections[$dbKey]);
        }
    }

    /**
     * 开启数据库事务
     *
     * @param string $whichDB 指定数据库标识
     * @return void
     */
    public function beginTransaction($whichDB)
    {
        $this->connections[$whichDB]->pdo->beginTransaction();
    }

    /**
     * 提交数据库事务
     *
     * @param string $whichDB 指定数据库标识
     * @return void
     */
    public function commit($whichDB)
    {
        $this->connections[$whichDB]->pdo->commit();
    }

    /**
     * 回滚数据库事务
     *
     * @param string $whichDB 指定数据库标识
     * @return void
     */
    public function rollback($whichDB)
    {
        $this->connections[$whichDB]->pdo->rollback();
    }
}
