<?php

namespace PhalApi\Foundation;

class Translator
{
    /**
     * @var array $message 翻译的映射
     */
    protected static $message = [];

    /**
     * @var string $language 语言
     */
    protected static $language = 'en';

    /**
     * 获取翻译
     *
     * @param string $key 翻译的内容
     * @param array $params 动态参数
     * @return mixed
     */
    public static function get($key, $params = [])
    {
        if (empty(self::$message)) {
            self::setLanguage('en');
        }

        $rs = isset(self::$message[$key]) ? self::$message[$key] : $key;

        $names = array_Keys($params);
        $names = array_map('\PhalApi\Foundation\Translator::formatVar', $names);

        return str_replace($names, array_values($params), $rs);
    }

    /**
     * 语言设置
     *
     * @param string $language 翻译包的目录名
     * @return void
     */
    public static function setLanguage($language)
    {
        self::$language = $language;

        self::$message = [];

        self::addMessage(dirname(__FILE__) . '/..');

        if (defined('API_ROOT')) {
            self::addMessage(API_ROOT);
        }
    }

    /**
     * 添加更多翻译
     *
     * @param string $path 待追加的路径
     * @return void
     */
    public static function addMessage($path)
    {
        $moreMessagePath = self::getMessageFilePath($path, self::$language);

        if (file_exists($moreMessagePath)) {
            self::$message = array_merge(self::$message, include $moreMessagePath);
        }
    }

    /**
     * 获取当前的语言
     *
     * @return string
     */
    public static function getLanguage()
    {
        return self::$language;
    }

    public static function formatVar($name)
    {
        return '{' . $name . '}';
    }

    protected static function getMessageFilePath($root, $language)
    {
        return implode(DIRECTORY_SEPARATOR, [$root, 'language', strtolower($language), 'common.php']);
    }
}
