<?php

namespace PhalApi\Cache;

use PhalApi\Contracts\Cache;

class NoneCache implements Cache
{
    public function set($key, $value, $expire = 600)
    {
    }

    public function get($key)
    {
        return NULL;
    }

    public function delete($key)
    {
    }
}
