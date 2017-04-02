<?php

namespace PhalApi\Responses;

class JsonP extends BaseResponse
{
    /**
     * @var string $callback JS回调函数名
     */
    protected $callback = '';

    public function __construct($callback)
    {
        $this->callback = $this->clearXss($callback);

        $this->addHeaders('Content-Type', 'text/javascript; charset=utf-8');
    }

    /**
     * 对回调函数进行跨站清除处理
     *
     * @param string $callback JS回调函数名
     * @return string
     */
    protected function clearXss($callback)
    {
        return $callback;
    }

    /**
     * @param array $result
     * @return mixed
     */
    protected function formatResult($result)
    {
        echo $this->callback . '(' . json_encode($result) . ')';
    }
}
