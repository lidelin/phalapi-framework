<?php

namespace PhalApi\Crypt;

use PhalApi\Contracts\Crypt;

class MultiMcrypt implements Crypt
{
    /**
     * @var \PhalApi\Crypt\Mcrypt $mcrypt
     */
    protected $mcrypt;

    public function __construct($iv)
    {
        $this->mcrypt = new Mcrypt($iv);
    }

    /**
     * 加密
     *
     * @param mixed $data 待加密的数据
     * @param $key
     * @return string
     */
    public function encrypt($data, $key)
    {
        $encryptData = serialize($data);

        $encryptData = $this->mcrypt->encrypt($encryptData, $key);

        $encryptData = base64_encode($encryptData);

        return $encryptData;
    }

    /**
     * 解密
     * 忽略不能正常反序列化的操作，并且在不能预期解密的情况下返回原文
     *
     * @param mixed $data 待解密的数据
     * @param $key
     * @return mixed
     */
    public function decrypt($data, $key)
    {
        $decryptData = base64_decode($data);

        if ($decryptData === false || $decryptData === '') {
            return $data;
        }

        $decryptData = $this->mcrypt->decrypt($decryptData, $key);

        $decryptData = @unserialize($decryptData);
        if ($decryptData === false) {
            return $data;
        }

        return $decryptData;
    }
}
