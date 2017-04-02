<?php

namespace PhalApi\Exceptions;

class BadRequest extends PhalApiException
{
    public function __construct($message, $code = 0)
    {
        parent::__construct(
            T('Bad Request: {message}', ['message' => $message]), 400 + $code
        );
    }
}
