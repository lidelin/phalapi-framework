<?php

namespace PhalApi\Crypt\RSA;

class MultiPub2Pri extends MultiBase
{
    protected $pub2pri;

    public function __construct()
    {
        $this->pub2pri = new Pub2Pri();

        parent::__construct();
    }

    protected function doEncrypt($toCryptPie, $pubkey)
    {
        return $this->pub2pri->encrypt($toCryptPie, $pubkey);
    }

    protected function doDecrypt($encryptPie, $prikey)
    {
        return $this->pub2pri->decrypt($encryptPie, $prikey);
    }
}
