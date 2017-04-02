<?php

namespace PhalApi\Crypt\RSA;

use PhalApi\Contracts\Crypt;

class Pub2Pri implements Crypt
{
    public function encrypt($data, $pubkey)
    {
        $rs = '';

        if (@openssl_public_encrypt($data, $rs, $pubkey) === false) {
            return null;
        }

        return $rs;
    }

    public function decrypt($data, $prikey)
    {
        $rs = '';

        if (@openssl_private_decrypt($data, $rs, $prikey) === false) {
            return null;
        }

        return $rs;
    }
}
