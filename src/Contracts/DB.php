<?php

namespace PhalApi\Contracts;

interface DB
{
    public function connect();

    public function disconnect();
}
