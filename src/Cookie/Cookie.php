<?php

namespace PhalApi\Cookie;

class Cookie
{
    /**
     * COOKIE配置
     */
    protected $config = [];

    /**
     * @param string $config ['path'] 路径
     * @param string $config ['domain'] 域名
     * @param boolean $config ['secure'] 是否加密
     * @param boolean $config ['httponly'] 是否只HTTP协议
     * @link http://php.net/manual/zh/function.setcookie.php
     */
    public function __construct($config = [])
    {
        $this->config['path'] = isset($config['path']) ? $config['path'] : null;
        $this->config['domain'] = isset($config['domain']) ? $config['domain'] : null;
        $this->config['secure'] = isset($config['secure']) ? $config['secure'] : false;
        $this->config['httponly'] = isset($config['httponly']) ? $config['httponly'] : false;
    }

    /**
     * 获取COOKIE
     *
     * @param string $name 待获取的COOKIE名字
     * @return string|null|array $name 为NULL时返回整个$_COOKIE，存在时返回COOKIE，否则返回null
     */
    public function get($name = null)
    {
        if ($name === null) {
            return $_COOKIE;
        }

        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    /**
     * 设置COOKIE
     *
     * @param string $name 待设置的COOKIE名字
     * @param string|int $value 建议COOKIE值为一些简单的字符串或数字，不推荐存放敏感数据
     * @param int $expire 有效期的timestamp，为NULL时默认存放一个月
     * @param boolean
     * @return bool
     */
    public function set($name, $value, $expire = null)
    {
        if ($expire === null) {
            $expire = $_SERVER['REQUEST_TIME'] + 2592000;   //a month
        }

        return setcookie(
            $name,
            $value,
            $expire,
            $this->config['path'],
            $this->config['domain'],
            $this->config['secure'],
            $this->config['httponly']
        );
    }

    /**
     * 删除COOKIE
     *
     * @param string $name 待删除的COOKIE名字
     * @param boolean
     * @return bool
     * @see \PhalApi\Cookie\Cookie::set()
     */
    public function delete($name)
    {
        return $this->set($name, '', 0);
    }

    /**
     * 获取COOKIE的配置
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
