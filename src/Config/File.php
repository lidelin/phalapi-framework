<?php

namespace PhalApi\Config;

use PhalApi\Contracts\Config;

class File implements Config
{
    /**
     * @var string $path 配置文件的目录位置
     */
    private $path = '';

    /**
     * @var array $map 配置文件的映射表，避免重复加载
     */
    private $map = [];

    public function __construct($configPath)
    {
        $this->path = $configPath;
    }

    /**
     * 获取配置
     * 首次获取时会进行初始化
     *
     * @param string $key 配置键值
     * @param mixed $default 默认值
     * @return mixed 需要获取的配置值
     */
    public function get($key, $default = null)
    {
        $keyArr = explode('.', $key);
        $fileName = $keyArr[0];

        if (!isset($this->map[$fileName])) {
            $this->loadConfig($fileName);
        }

        $rs = null;
        $preRs = $this->map;
        foreach ($keyArr as $subKey) {
            if (!isset($preRs[$subKey])) {
                $rs = null;
                break;
            }
            $rs = $preRs[$subKey];
            $preRs = $rs;
        }

        return $rs !== null ? $rs : $default;
    }

    /**
     * 加载配置文件
     * 加载保存配置信息数组的config.php文件，若文件不存在，则将$map置为空数组
     *
     * @param string $fileName 配置文件路径
     * @return array 配置文件对应的内容
     */
    private function loadConfig($fileName)
    {
        $config = include($this->path . DIRECTORY_SEPARATOR . $fileName . '.php');

        $this->map[$fileName] = $config;
    }
}
