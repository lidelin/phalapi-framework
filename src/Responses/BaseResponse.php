<?php

namespace PhalApi\Responses;

abstract class BaseResponse
{
    /**
     * @var int $ret 返回状态码，其中：200成功，400非法请求，500服务器错误
     */
    protected $ret = 200;

    /**
     * @var array $data 待返回给客户端的数据
     */
    protected $data = [];

    /**
     * @var string $msg 错误返回信息
     */
    protected $msg = '';

    /**
     * @var array $headers 响应报文头部
     */
    protected $headers = [];

    /**
     * 设置返回状态码
     *
     * @param int $ret 返回状态码，其中：200成功，400非法请求，500服务器错误
     * @return $this
     */
    public function setRet($ret)
    {
        $this->ret = $ret;

        return $this;
    }

    /**
     * 设置返回数据
     *
     * @param array|string $data 待返回给客户端的数据，建议使用数组，方便扩展升级
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * 设置错误信息
     *
     * @param string $msg 错误信息
     * @return $this
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;

        return $this;
    }

    /**
     * 添加报文头部
     *
     * @param string $key 名称
     * @param string $content 内容
     * @return void
     */
    public function addHeaders($key, $content)
    {
        $this->headers[$key] = $content;
    }

    /**
     * 结果输出
     */
    public function output()
    {
        $this->handleHeaders($this->headers);

        $rs = $this->getResult();

        echo $this->formatResult($rs);
    }

    public function getResult()
    {
        $rs = [
            'ret' => $this->ret,
            'data' => $this->data,
            'msg' => $this->msg,
        ];

        return $rs;
    }

    /**
     * 获取头部
     *
     * @param string $key 头部的名称
     * @return array|mixed|null
     */
    public function getHeaders($key = null)
    {
        if (null === $key) {
            return $this->headers;
        }

        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    protected function handleHeaders($headers)
    {
        foreach ($headers as $key => $content) {
            @header($key . ': ' . $content);
        }
    }

    /**
     * 格式化需要输出返回的结果
     *
     * @param array $result 待返回的结果数据
     */
    abstract protected function formatResult($result);
}