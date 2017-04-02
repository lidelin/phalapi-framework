<?php

namespace PhalApi\Crypt\RSA;

use PhalApi\Contracts\Crypt;

abstract class MultiBase implements Crypt
{
    /**
     * @var int 用户最大分割长度
     */
    protected $maxSplitLen;

    /**
     * @var int 允许最大分割的长度
     */
    const ALLOW_MAX_SPLIT_LEN = 117;

    /**
     * @param int $maxSplitLen 最大分割的彻底，应介于(0, PhalApi_Crypt_RSA_MultiBase::ALLOW_MAX_SPLIT_LEN]
     */
    public function __construct($maxSplitLen = 0)
    {
        $this->maxSplitLen = $maxSplitLen > 0
            ? min($maxSplitLen, self::ALLOW_MAX_SPLIT_LEN) : self::ALLOW_MAX_SPLIT_LEN;
    }

    /**
     * 加密
     *
     * @param string $data 待加密的字符串，注意其他类型会强制转成字符串再处理
     * @param string $key 私钥/公钥
     * @return string|null 失败时返回null
     */
    public function encrypt($data, $key)
    {
        $base64Data = base64_encode(strval($data));

        $base64DataArr = str_split($base64Data, $this->getMaxSplitLen());

        $encryptPieCollector = [];
        foreach ($base64DataArr as $toCryptPie) {
            $encryptPie = $this->doEncrypt($toCryptPie, $key);
            if ($encryptPie === null) {
                return null;
            }
            $encryptPieCollector[] = base64_encode($encryptPie);
        }

        return base64_encode(json_encode($encryptPieCollector));
    }

    /**
     * 具体的加密操作
     *
     * @param string $toCryptPie 待加密的片段
     * @param string $key 公钥/私钥
     */
    abstract protected function doEncrypt($toCryptPie, $key);

    /**
     * 解密
     *
     * @param string $data 待解密的字符串
     * @param string $key 公钥/私钥
     * @return string|null 失败时返回null
     */
    public function decrypt($data, $key)
    {
        if ($data === null || $data === '') {
            return $data;
        }

        $encryptPieCollector = @json_decode(base64_decode($data), true);
        if (!is_array($encryptPieCollector)) {
            return null;
        }

        $decryptPieCollector = [];
        foreach ($encryptPieCollector as $encryptPie) {
            $base64DecryptPie = @base64_decode($encryptPie);
            if ($base64DecryptPie === false) {
                return null;
            }
            $decryptPie = $this->doDecrypt($base64DecryptPie, $key);
            if ($decryptPie === null) {
                return null;
            }
            $decryptPieCollector[] = $decryptPie;
        }

        $decryptData = implode('', $decryptPieCollector);

        $rs = @base64_decode($decryptData);

        return $rs !== false ? $rs : null;
    }

    /**
     * 具体加密的操作
     * @param string $encryptPie 待加密的片段
     * @param string $key 公钥/私钥
     */
    abstract protected function doDecrypt($encryptPie, $key);

    /**
     * 取用户设置的取大分割长度
     */
    protected function getMaxSplitLen()
    {
        return $this->maxSplitLen;
    }
}
