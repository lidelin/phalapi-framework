<?php

use PhalApi\Foundation\DI;
use PhalApi\Foundation\Translator;

if (!function_exists('DI')) {
    /**
     * 获取 DI，相当于 PhalApi\Foundation\DI::one()
     *
     * @return DI
     */
    function DI()
    {
        return DI::one();
    }
}

if (!function_exists('SL')) {
    /**
     * 设定语言，SL为setLanguage的简写
     * @param string $language 翻译包的目录名
     */
    function SL($language)
    {
        Translator::setLanguage($language);
    }
}

if (!function_exists('T')) {
    /**
     * 快速翻译
     *
     * @param string $msg 待翻译的内容
     * @param array $params 动态参数
     *
     * @return mixed
     */
    function T($msg, $params = [])
    {
        return Translator::get($msg, $params);
    }
}
