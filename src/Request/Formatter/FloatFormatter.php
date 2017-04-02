<?php

namespace PhalApi\Request\Formatter;

use PhalApi\Contracts\Request\Formatter;

class FloatFormatter extends BaseFormatter implements Formatter
{
    /**
     * 对浮点型进行格式化
     *
     * @param mixed $value 变量值
     * @param array $rule ['min' => '最小值', 'max' => '最大值']
     * @return float|string 格式化后的变量
     */
    public function parse($value, $rule)
    {
        return floatval($this->filterByRange(floatval($value), $rule));
    }
}
