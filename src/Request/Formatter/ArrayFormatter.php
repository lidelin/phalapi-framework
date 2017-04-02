<?php

namespace PhalApi\Request\Formatter;

use PhalApi\Contracts\Request\Formatter;

class ArrayFormatter extends BaseFormatter implements Formatter
{
    /**
     * 对数组格式化/数组转换
     *
     * @param string $value 变量值
     * @param array $rule ['name' => '', 'type' => 'array', 'default' => '', 'format' => 'json/explode', 'separator' => '', 'min' => '', 'max' => '']
     * @return array
     */
    public function parse($value, $rule)
    {
        $rs = $value;

        if (!is_array($rs)) {
            $ruleFormat = !empty($rule['format']) ? strtolower($rule['format']) : '';
            if ($ruleFormat == 'explode') {
                $rs = explode(isset($rule['separator']) ? $rule['separator'] : ',', $rs);
            } else if ($ruleFormat == 'json') {
                $rs = json_decode($rs, true);
            } else {
                $rs = [$rs];
            }
        }

        $this->filterByRange(count($rs), $rule);

        return $rs;
    }
}
