<?php

namespace PhalApi\Filter;

use PhalApi\Contracts\Filter;
use PhalApi\Exceptions\BadRequest;

class SimpleMD5 implements Filter
{
    protected $signName;

    public function __construct($signName = 'sign')
    {
        $this->signName = $signName;
    }

    public function check()
    {
        $allParams = DI()->request->getAll();
        if (empty($allParams)) {
            return;
        }

        $sign = isset($allParams[$this->signName]) ? $allParams[$this->signName] : '';
        unset($allParams[$this->signName]);

        $expectSign = $this->encryptAppKey($allParams);

        if ($expectSign != $sign) {
            DI()->logger->debug('Wrong Sign', ['needSign' => $expectSign]);
            throw new BadRequest(T('wrong sign'), 6);
        }
    }

    protected function encryptAppKey($params)
    {
        ksort($params);

        $paramsStrExceptSign = '';
        foreach ($params as $val) {
            $paramsStrExceptSign .= $val;
        }

        return md5($paramsStrExceptSign);
    }
}
