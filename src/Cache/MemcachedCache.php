<?php

namespace PhalApi\Cache;

use Memcached;

class MemcachedCache extends MemcacheCache
{
    /**
     * 注意参数的微妙区别
     *
     * @param $key
     * @param $value
     * @param $expire
     */
    public function set($key, $value, $expire = 600)
    {
        $this->memcache->set($this->formatKey($key), @serialize($value), $expire);
    }

    /**
     * 返回更高版本的MC实例
     *
     * @return Memcached
     */
    protected function createMemcache()
    {
        return new Memcached();
    }
}
