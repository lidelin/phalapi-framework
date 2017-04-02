<?php

namespace PhalApi\Crypt\RSA;

use PhalApi\Contracts\Crypt;

class Pri2Pub implements Crypt
{
    public function encrypt($data, $prikey)
    {
        $rs = '';

        if (@openssl_private_encrypt($data, $rs, $prikey) === false) {
            return null;
        }

        return $rs;
    }

    public function decrypt($data, $pubkey)
    {
        $rs = '';

        if (@openssl_public_decrypt($data, $rs, $pubkey) === false) {
            return null;
        }

        return $rs;
    }
}
