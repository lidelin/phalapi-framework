<?php

namespace PhalApi\Contracts\Request;

interface Formatter
{
    public function parse($value, $rule);
}