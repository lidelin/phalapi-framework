<?php

namespace PhalApi\Foundation;

use Exception;
use PhalApi\Exceptions\PhalApiException;

class PhalApi
{
    /**
     * 项目根目录
     *
     * @var string
     */
    protected $basePath;

    /**
     * 项目命名空间
     *
     * @var string
     */
    protected $namespace;

    /**
     * PhalApi constructor.
     *
     * @param null|string $basePath
     * @param string $namespace
     */
    public function __construct($basePath = null, $namespace = 'Demo')
    {
        $this->setBasePath($basePath);
        $this->setNamespace($namespace);
    }

    /**
     * 设置项目根目录
     *
     * @param  string  $basePath
     * @return void
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
    }

    /**
     * 获取项目根目录
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * 设置项目命名空间
     *
     * @param string $namespace 命名空间
     * @return void
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * 获取项目命名空间
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    public function response()
    {
        $rs = DI()->response;
        $service = DI()->request->get('service', 'Index.Index');

        try {
            // 接口响应
            $api = ApiFactory::generateService($this->namespace);
            list($apiClassName, $action) = explode('.', $service);
            $data = call_user_func([$api, $action]);

            $rs->setData($data);
        } catch (PhalApiException $ex) {
            // 框架或项目的异常
            $rs->setRet($ex->getCode());
            $rs->setMsg($ex->getMessage());
        } catch (Exception $ex) {
            // 不可控的异常
            DI()->logger->error($service, strval($ex));
            throw $ex;
        }

        return $rs;
    }
}
