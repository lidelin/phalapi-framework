<?php

namespace PhalApi\Request\Formatter;

use PhalApi\Contracts\Request\Formatter;
use PhalApi\Exceptions\InternalServerError;

class EnumFormatter extends BaseFormatter implements Formatter
{
    /**
     * 检测枚举类型
     *
     * @param string $value 变量值
     * @param array $rule ['name' => '', 'type' => 'enum', 'default' => '', 'range' => []]
     * @return mixed
     */
    public function parse($value, $rule)
    {
        $this->formatEnumRule($rule);

        $this->formatEnumValue($value, $rule);

        return $value;
    }

    /**
     * 检测枚举规则的合法性
     *
     * @param array $rule ['name' => '', 'type' => 'enum', 'default' => '', 'range' => []]
     * @throws InternalServerError
     */
    protected function formatEnumRule($rule)
    {
        if (!isset($rule['range'])) {
            throw new InternalServerError(
                T("miss {name}'s enum range", ['name' => $rule['name']]));
        }

        if (empty($rule['range']) || !is_array($rule['range'])) {
            throw new InternalServerError(
                T("{name}'s enum range can not be empty", ['name' => $rule['name']]));
        }
    }
}
