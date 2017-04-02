<?php

namespace PhalApi\Foundation;

use ArrayAccess;
use Closure;
use PhalApi\Exceptions\InternalServerError;

/**
 * Class DI
 *
 * @property \PhalApi\Foundation\Request            $request    请求
 * @property \PhalApi\Responses\Json                $response   结果响应
 * @property \PhalApi\Contracts\Crypt               $crypt      加密
 * @property \PhalApi\Contracts\Config              $config     配置
 * @property \PhalApi\Foundation\DatabaseManager    $database   数据库
 */
class DI implements ArrayAccess
{
    /**
     * @var \PhalApi\Foundation\DI $instance 单例
     */
    protected static $instance;

    /**
     * @var array $hitTimes 服务命中的次数
     */
    protected $hitTimes = [];

    /**
     * @var array $data 注册的服务池
     */
    protected $data = [];

    /**
     * 获取 DI 单体实例
     */
    public static function one()
    {
        if (null === self::$instance) {
            self::$instance = new static();
            self::$instance->onConstruct();
        }

        return self::$instance;
    }

    /**
     * service 级的构造函数
     */
    public function onConstruct()
    {
        $this->request = \PhalApi\Foundation\Request::class;
        $this->response = \PhalApi\Responses\Json::class;
    }

    /**
     * 统一 setter
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->resetHit($key);

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * 统一 getter
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = $default;
        }

        $this->recordHitTimes($key);

        if ($this->isFirstHit($key)) {
            $this->data[$key] = $this->initService($this->data[$key]);
        }

        return $this->data[$key];
    }

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'set') {
            $key = lcfirst(substr($name, 3));
            return $this->set($key, isset($arguments[0]) ? $arguments[0] : null);
        } elseif (substr($name, 0, 3) == 'get') {
            $key = lcfirst(substr($name, 3));
            return $this->get($key, isset($arguments[0]) ? $arguments[0] : null);
        }

        throw new InternalServerError(
            T('Call to undefined method PhalApi_DI::{name}() .', ['name' => $name])
        );
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name, null);
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        $this->get($offset, null);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    protected function resetHit($key)
    {
        $this->hitTimes[$key] = 0;
    }

    protected function recordHitTimes($key)
    {
        if (!isset($this->hitTimes[$key])) {
            $this->hitTimes[$key] = 0;
        }

        $this->hitTimes[$key]++;
    }

    protected function isFirstHit($key)
    {
        return $this->hitTimes[$key] == 1;
    }

    protected function initService($config)
    {
        $rs = null;

        if ($config instanceof Closure) {
            $rs = $config();
        } elseif (is_string($config) && class_exists($config)) {
            $rs = new $config();

            if (is_callable([$rs, 'onInitialize'])) {
                call_user_func([$rs, 'onInitialize']);
            }
        } else {
            $rs = $config;
        }

        return $rs;
    }
}
