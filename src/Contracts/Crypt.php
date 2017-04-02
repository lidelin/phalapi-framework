<?php

namespace PhalApi\Contracts;

interface Crypt
{
    /**
     * 对称加密
     *
     * @param mixed $data 等加密的数据
     * @param string $key 加密的key
     * @return mixed 加密后的数据
     */
    public function encrypt($data, $key);

    /**
     * 对称解密
     *
     * @param mixed $data 对称加密后的内容
     * @param string $key 加密的key
     * @return mixed 解密后的数据
     */
    public function decrypt($data, $key);
}
