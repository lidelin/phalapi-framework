<?php

namespace PhalApi\Crypt\RSA;

class KeyGenerator
{
    protected $privkey;

    protected $pubkey;

    public function __construct()
    {
        $res = openssl_pkey_new();
        openssl_pkey_export($res, $privkey);
        $this->privkey = $privkey;

        $pubkey = openssl_pkey_get_details($res);
        $this->pubkey = $pubkey['key'];
    }

    public function getPriKey()
    {
        return $this->privkey;
    }

    public function getPubKey()
    {
        return $this->pubkey;
    }
}
