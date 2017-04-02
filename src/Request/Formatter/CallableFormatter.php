<?php

namespace PhalApi\Request\Formatter;

use PhalApi\Contracts\Request\Formatter;
use PhalApi\Exceptions\InternalServerError;

class CallableFormatter extends BaseFormatter implements Formatter
{
    /**
     * 对回调类型进行格式化
     *
     * @param mixed $value 变量值
     * @param array $rule ['callback' => '回调函数', 'params' => '第三个参数']
     * @throws InternalServerError
     * @return boolean|string 格式化后的变量
     */
    public function parse($value, $rule)
    {
        if (!isset($rule['callback']) || !is_callable($rule['callback'])) {
            throw new InternalServerError(
                T('invalid callback for rule: {name}', ['name' => $rule['name']])
            );
        }

        if (isset($rule['params'])) {
            return call_user_func($rule['callback'], $value, $rule, $rule['params']);
        } else {
            return call_user_func($rule['callback'], $value, $rule);
        }
    }
}
