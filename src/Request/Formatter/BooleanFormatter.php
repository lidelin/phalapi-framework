<?php

namespace PhalApi\Request\Formatter;

use PhalApi\Contracts\Request\Formatter;

class BooleanFormatter extends BaseFormatter implements Formatter
{
    /**
     * 对布尔型进行格式化
     *
     * @param mixed $value 变量值
     * @param array $rule ['true' => '成立时替换的内容', 'false' => '失败时替换的内容']
     * @return boolean/string 格式化后的变量
     */
    public function parse($value, $rule)
    {
        $rs = $value;

        if (!is_bool($value)) {
            if (is_numeric($value)) {
                $rs = $value > 0 ? true : false;
            } else if (is_string($value)) {
                $rs = in_array(strtolower($value), ['ok', 'true', 'success', 'on', 'yes'])
                    ? true : false;
            } else {
                $rs = $value ? true : false;
            }
        }

        return $rs;
    }
}
