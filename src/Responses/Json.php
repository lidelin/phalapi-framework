<?php

namespace PhalApi\Responses;

class Json extends BaseResponse
{
    public function __construct()
    {
        $this->addHeaders('Content-Type', 'application/json;charset=utf-8');
    }

    /**
     * 格式化需要输出返回的结果
     *
     * @param array $result
     * @return string
     */
    protected function formatResult($result)
    {
        return json_encode($result);
    }
}
