<?php

namespace PhalApi\Request;

use PhalApi\Contracts\Request\Formatter;
use PhalApi\Exceptions\InternalServerError;

class Variable
{
    public static function format($varName, $rule, $params)
    {
        $value = isset($rule['default']) ? $rule['default'] : null;
        $type = !empty($rule['type']) ? strtolower($rule['type']) : 'string';

        $key = isset($rule['name']) ? $rule['name'] : $varName;
        $value = isset($params[$key]) ? $params[$key] : $value;

        if ($value === null && $type != 'file') {
            return $value;
        }

        return self::formatAllType($type, $value, $rule);
    }

    protected static function formatAllType($type, $value, $rule)
    {
        $diKey = '_formatter' . ucfirst($type);
        $diDefault = 'PhalApi\Request\Formatter\\' . ucfirst($type) . 'Formatter';

        $formatter = DI()->get($diKey, $diDefault);

        if (!($formatter instanceof Formatter)) {
            throw new InternalServerError(
                T('invalid type: {type} for rule: {name}', ['type' => $type, 'name' => $rule['name']])
            );
        }

        return $formatter->parse($value, $rule);
    }
}