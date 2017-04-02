<?php

namespace PhalApi\Crypt;

use PhalApi\Contracts\Crypt;
use PhalApi\Exceptions\InternalServerError;

class Mcrypt implements Crypt
{
    /**
     * @var string $iv 加密向量， 最大长度不得超过 \PhalApi\Crypt\Mcrypt::MAX_IV_SIZE
     */
    protected $iv;

    /**
     * @var int 最大加密向量长度
     */
    const MAX_IV_SIZE = 8;

    /**
     * @var int 最大加密key的长度
     */
    const MAX_KEY_LENGTH = 56;

    /**
     * @param string $iv 加密的向量 最大长度不得超过 MAX_IV_SIZE
     */
    public function __construct($iv = '********')
    {
        $this->iv = str_pad($iv, self::MAX_IV_SIZE, '*');
        if (strlen($this->iv) > self::MAX_IV_SIZE) {
            $this->iv = substr($this->iv, 0, self::MAX_IV_SIZE);
        }
    }

    /**
     * 对称加密
     *
     * @param string $data 待加密的数据
     * @param string $key 私钥
     * @return string 加密后的数据
     */
    public function encrypt($data, $key)
    {
        if ($data === '') {
            return $data;
        }

        $cipher = $this->createCipher($key);

        $encrypted = mcrypt_generic($cipher, $data);

        $this->clearCipher($cipher);

        return $encrypted;
    }

    /**
     * 对称解密
     *
     * @param string $data 待解密的数据
     * @param string $key 私钥
     * @return string 解密后的数据
     * @see \PhalApi\Crypt\Mcrypt::encrypt()
     */
    public function decrypt($data, $key)
    {
        if ($data === '') {
            return $data;
        }

        $cipher = $this->createCipher($key);

        $decrypted = mdecrypt_generic($cipher, $data);

        $this->clearCipher($cipher);

        return rtrim($decrypted, "\0");
    }

    /**
     * 创建cipher
     *
     * @param string $key 私钥
     * @throws InternalServerError
     * @return resource
     */
    protected function createCipher($key)
    {
        $cipher = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');

        if ($cipher === false || $cipher < 0) {
            throw new InternalServerError(
                T('mcrypt_module_open with {cipher}', ['cipher' => $cipher])
            );
        }

        mcrypt_generic_init($cipher, $this->formatKey($key), $this->iv);

        return $cipher;
    }

    /**
     * 格式化私钥
     *
     * @param string $key 私钥
     * @return int
     */
    protected function formatKey($key)
    {
        return strlen($key) > self::MAX_KEY_LENGTH ? substr($key, 0, self::MAX_KEY_LENGTH) : $key;
    }

    /**
     * 释放cipher
     *
     * @param resource $cipher
     */
    protected function clearCipher($cipher)
    {
        mcrypt_generic_deinit($cipher);
        mcrypt_module_close($cipher);
    }
}
