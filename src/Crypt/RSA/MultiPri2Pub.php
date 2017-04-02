<?php

namespace PhalApi\Crypt\RSA;

class MultiPri2Pub extends MultiBase
{

    protected $pri2pub;

    public function __construct()
    {
        $this->pri2pub = new Pri2Pub();

        parent::__construct();
    }

    protected function doEncrypt($toCryptPie, $prikey)
    {
        return $this->pri2pub->encrypt($toCryptPie, $prikey);
    }

    protected function doDecrypt($encryptPie, $prikey)
    {
        return $this->pri2pub->decrypt($encryptPie, $prikey);
    }
}
