<?php

namespace PhalApi\Cache;

use PhalApi\Contracts\Cache;
use PhalApi\Exceptions\InternalServerError;

class FileCache implements Cache
{
    protected $folder;

    protected $prefix;

    public function __construct($config)
    {
        $this->folder = rtrim($config['path'], '/');

        $cacheFolder = $this->createCacheFileFolder();

        if (!is_dir($cacheFolder)) {
            mkdir($cacheFolder, 0777, true);
        }

        $this->prefix = isset($config['prefix']) ? $config['prefix'] : 'phapapi';
    }

    public function set($key, $value, $expire = 600)
    {
        if ($key === null || $key === '') {
            return;
        }

        $filePath = $this->createCacheFilePath($key);

        $expireStr = sprintf('%010d', $expire + time());
        if (strlen($expireStr) > 10) {
            throw new InternalServerError(
                T('file expire is too large')
            );
        }

        if (!file_exists($filePath)) {
            touch($filePath);
            chmod($filePath, 0777);
        }
        file_put_contents($filePath, $expireStr . serialize($value));
    }

    public function get($key)
    {
        $filePath = $this->createCacheFilePath($key);

        if (file_exists($filePath)) {
            $expireTime = file_get_contents($filePath, false, null, 0, 10);

            if ($expireTime > time()) {
                return @unserialize(file_get_contents($filePath, false, null, 10));
            }
        }

        return null;
    }

    public function delete($key)
    {
        if ($key === null || $key === '') {
            return;
        }

        $filePath = $this->createCacheFilePath($key);

        @unlink($filePath);
    }

    /**
     * 考虑到Linux同一目录下的文件个数限制，这里拆分成1000个文件缓存目录
     *
     * @param string $key 缓存key
     * @return string 路径
     */
    protected function createCacheFilePath($key)
    {
        $folderSufix = sprintf('%03d', hexdec(substr(sha1($key), -5)) % 1000);
        $cacheFolder = $this->createCacheFileFolder() . DIRECTORY_SEPARATOR . $folderSufix;
        if (!is_dir($cacheFolder)) {
            mkdir($cacheFolder, 0777, true);
        }

        return $cacheFolder . DIRECTORY_SEPARATOR . md5($key) . '.dat';
    }

    protected function createCacheFileFolder()
    {
        return $this->folder . DIRECTORY_SEPARATOR . 'cache';
    }
}

