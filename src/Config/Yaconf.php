<?php

namespace PhalApi\Config;

use PhalApi\Contracts\Config;

class Yaconf implements Config
{
    public function get($key, $default = null)
    {
        return Yaconf::get($key, $default);
    }

    public function has($key)
    {
        return Yaconf::has($key);
    }
}
