<?php

namespace PhalApi\Request\Formatter;

use PhalApi\Contracts\Request\Formatter;

class DateFormatter extends BaseFormatter implements Formatter
{
    /**
     * 对日期进行格式化
     *
     * @param int $value 变量值
     * @param array $rule ['format' => 'timestamp', 'min' => '最小值', 'max' => '最大值']
     * @return int|string 格式化后的变量
     */
    public function parse($value, $rule)
    {
        $rs = $value;

        $ruleFormat = !empty($rule['format']) ? strtolower($rule['format']) : '';
        if ($ruleFormat == 'timestamp') {
            $rs = strtotime($value);
            if ($rs <= 0) {
                $rs = 0;
            }

            $rs = $this->filterByRange($rs, $rule);
        }

        return $rs;
    }
}
