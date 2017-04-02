<?php

namespace PhalApi\Contracts;

interface Config
{
    /**
     * 获取配置
     *
     * @param $key string 配置键值
     * @param mixed $default 缺省值
     * @return mixed 需要获取的配置值，不存在时统一返回$default
     */
    public function get($key, $default = null);
}
