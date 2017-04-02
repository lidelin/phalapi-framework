<?php

namespace PhalApi\Cache;

use PhalApi\Contracts\Cache;

class MultiCache implements Cache
{
    protected $caches = [];

    public function addCache(Cache $cache)
    {
        $this->caches[] = $cache;
    }

    public function set($key, $value, $expire = 600)
    {
        foreach ($this->caches as $cache) {
            $cache->set($key, $value, $expire);
        }
    }

    public function get($key)
    {
        foreach ($this->caches as $cache) {
            $value = $cache->get($key);
            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    public function delete($key)
    {
        foreach ($this->caches as $cache) {
            $cache->delete($key);
        }
    }
}
