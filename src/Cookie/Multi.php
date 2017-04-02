<?php

namespace PhalApi\Cookie;

class Multi extends Cookie
{
    /**
     * @param $config ['crypt'] 加密的服务，如果未设置，默认取DI()->crypt，须实现 \PhalApi\Contracts\Crypt 接口
     * @param $config ['key'] $config['crypt']用的密钥，未设置时有一个md5串
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->config['crypt'] = isset($config['crypt']) ? $config['crypt'] : DI()->crypt;

        if (isset($config['crypt']) && $config['crypt'] instanceof \PhalApi\Contracts\Crypt) {
            $this->config['key'] = isset($config['key'])
                ? $config['key'] : 'debcf37743b7c835ba367548f07aadc3';
        } else {
            $this->config['crypt'] = null;
        }
    }

    /**
     * 解密获取COOKIE
     *
     * @param $name
     * @return string|null|array
     * @see \PhalApi\Cookie\Cookie::get()
     */
    public function get($name = null)
    {
        $rs = parent::get($name);

        if (!isset($this->config['crypt'])) {
            return $rs;
        }

        if (is_array($rs)) {
            foreach ($rs as &$valueRef) {
                $this->config['crypt']->decrypt($valueRef, $this->config['key']);
            }
        } else if ($rs !== null) {
            $rs = $this->config['crypt']->decrypt($rs, $this->config['key']);
        }

        return $rs;
    }

    /**
     * 加密设置COOKIE&记忆功能
     *
     * @param $name
     * @param $value
     * @param $expire
     * @return bool
     * @see \PhalApi\Cookie\Cookie::set()
     */
    public function set($name, $value, $expire = null)
    {
        if (isset($this->config['crypt'])) {
            $value = $this->config['crypt']->encrypt($value, $this->config['key']);
        }

        $_COOKIE[$name] = $value;
        if ($expire < $_SERVER['REQUEST_TIME']) {
            unset($_COOKIE[$name]);
        }

        return parent::set($name, $value, $expire);
    }
}
