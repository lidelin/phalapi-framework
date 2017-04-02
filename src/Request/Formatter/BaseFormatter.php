<?php

namespace PhalApi\Request\Formatter;

use PhalApi\Exceptions\InternalServerError;
use PhalApi\Exceptions\BadRequest;

class BaseFormatter
{
    /**
     * 根据范围进行控制
     *
     * @param $value
     * @param $rule
     * @return mixed
     */
    protected function filterByRange($value, $rule)
    {
        $this->filterRangeMinLessThanOrEqualsMax($rule);

        $this->filterRangeCheckMin($value, $rule);

        $this->filterRangeCheckMax($value, $rule);

        return $value;
    }

    protected function filterRangeMinLessThanOrEqualsMax($rule)
    {
        if (isset($rule['min']) && isset($rule['max']) && $rule['min'] > $rule['max']) {
            throw new InternalServerError(
                T('min should <= max, but now {name} min = {min} and max = {max}',
                    ['name' => $rule['name'], 'min' => $rule['min'], 'max' => $rule['max']])
            );
        }
    }

    protected function filterRangeCheckMin($value, $rule)
    {
        if (isset($rule['min']) && $value < $rule['min']) {
            throw new InternalServerError(
                T('{name} should >= {min}, but now {name} = {value}',
                    ['name' => $rule['name'], 'min' => $rule['min'], 'value' => $value])
            );
        }
    }

    protected function filterRangeCheckMax($value, $rule)
    {
        if (isset($rule['max']) && $value > $rule['max']) {
            throw new BadRequest(
                T('{name} should <= {max}, but now {name} = {value}',
                    ['name' => $rule['name'], 'max' => $rule['max'], 'value' => $value])
            );
        }
    }

    /**
     * 格式化枚举类型
     *
     * @param string $value 变量值
     * @param array $rule ['name' => '', 'type' => 'enum', 'default' => '', 'range' => []]
     * @throws BadRequest
     */
    protected function formatEnumValue($value, $rule)
    {
        if (!in_array($value, $rule['range'])) {
            throw new BadRequest(
                T('{name} should be in {range}, but now {name} = {value}',
                    ['name' => $rule['name'], 'range' => implode('/', $rule['range']), 'value' => $value])
            );
        }
    }
}
