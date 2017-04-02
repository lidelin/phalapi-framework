<?php

namespace PhalApi\Exceptions;

class InternalServerError extends PhalApiException
{
    public function __construct($message, $code = 0)
    {
        parent::__construct(
            T('Interal Server Error: {message}', ['message' => $message]), 500 + $code
        );
    }
}
