<?php

namespace PhalApi\Foundation;

use PhalApi\Exceptions\BadRequest;
use PhalApi\Exceptions\InternalServerError;

class ApiFactory
{
    static function generateService($namespace, $isInitialize = true)
    {
        $service = DI()->request->get('service', 'Index.Index');

        $serviceArr = explode('.', $service);

        if (count($serviceArr) < 2) {
            throw new BadRequest(
                T('service ({service}) illegal', ['service' => $service])
            );
        }

        list ($apiClassName, $action) = $serviceArr;
        $apiClassName = $namespace . '\Api\\' . ucfirst($apiClassName);
        $action = lcfirst($action);

        if (!class_exists($apiClassName)) {
            throw new BadRequest(
                T('no such service as {service}', ['service' => $service])
            );
        }

        $api = new $apiClassName();

        if (!is_subclass_of($api, \PhalApi\Foundation\Api::class)) {
            throw new InternalServerError(
                T('{class} should be subclass of \PhalApi\Foundation\Api', ['class' => $apiClassName])
            );
        }

        if (!method_exists($api, $action) || !is_callable([$api, $action])) {
            throw new BadRequest(
                T('no such service as {service}', ['service' => $service])
            );
        }

        if ($isInitialize) {
            $api->init();
        }

        return $api;
    }
}
