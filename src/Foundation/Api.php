<?php

namespace PhalApi\Foundation;

use PhalApi\Exceptions\InternalServerError;
use PhalApi\Exceptions\BadRequest;

class Api
{
    /**
     * 设置规则解析后的接口参数
     *
     * @param string $name 接口参数名字
     * @param mixed $value 接口参数解析后的值
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * 获取规则解析后的接口参数
     *
     * @param string $name 接口参数名字
     * @throws InternalServerError 获取未设置的接口参数时，返回500
     * @return mixed
     */
    public function __get($name)
    {
        if (!isset($this->$name) || empty($name)) {
            throw new InternalServerError(
                T('PhalApi_Api::${name} undefined', ['name' => $name])
            );
        }

        return $this->$name;
    }

    /**
     * 初始化
     *
     * 主要完成的初始化工作有：
     * - 1、[必须]按参数规则解析生成接口参数
     * - 2、[可选]过滤器调用，如：签名验证
     * - 3、[可选]用户身份验证
     *
     * @uses \PhalApi\Foundation\Api::createMemberValue()
     * @uses \PhalApi\Foundation\Api::filterCheck()
     * @uses \PhalApi\Foundation\Api::userCheck()
     *
     * @return void
     */
    public function init()
    {
        $this->createMemberValue();

//        $this->filterCheck();

        $this->userCheck();
    }

    /**
     * 按参数规则解析生成接口参数
     *
     * 根据配置的参数规则，解析过滤，并将接口参数存放于类成员变量
     *
     * @uses \PhalApi\Foundation\Api::getApiRules()
     */
    protected function createMemberValue()
    {
        foreach ($this->getApiRules() as $key => $rule) {
            $this->$key = DI()->request->getByRule($rule);
        }
    }

    /**
     * 取接口参数规则
     *
     * 主要包括有：
     * - 1、[固定]系统级的service参数
     * - 2、应用级统一接口参数规则，在app.apiCommonRules中配置
     * - 3、接口级通常参数规则，在子类的*中配置
     * - 4、接口级当前操作参数规则
     *
     * <b>当规则有冲突时，以后面为准。另外，被请求的函数名和配置的下标都转成小写再进行匹配。</b>
     *
     * @uses \PhalApi\Foundation\Api::getRules()
     *
     * @return array
     */
    public function getApiRules()
    {
        $rules = [];

        $allRules = $this->getRules();
        if (!is_array($allRules)) {
            $allRules = [];
        }

        $allRules = array_change_key_case($allRules, CASE_LOWER);

        $service = DI()->request->get('service', 'Default.Index');
        list($apiClassName, $action) = explode('.', $service);
        $action = strtolower($action);

        if (isset($allRules[$action]) && is_array($allRules[$action])) {
            $rules = $allRules[$action];
        }
        if (isset($allRules['*'])) {
            $rules = array_merge($allRules['*'], $rules);
        }

        $apiCommonRules = DI()->config->get('app.apiCommonRules', []);
        if (!empty($apiCommonRules) && is_array($apiCommonRules)) {
            $rules = array_merge($apiCommonRules, $rules);
        }

        return $rules;
    }

    /**
     * 获取参数设置的规则
     *
     * 可由开发人员根据需要重载
     *
     * @return array
     */
    public function getRules()
    {
        return [];
    }

    /**
     * 过滤器调用
     *
     * 可由开发人员根据需要重载，以实现项目拦截处理，需要：
     * - 1、实现PhalApi_Filter::check()接口
     * - 2、注册的过滤器到DI()->filter
     *
     * <br>以下是一个简单的示例：<br>
     * ```
     *    class My_Filter implements PhalApi_Filter {
     *
     *        public function check() {
     *            //TODO
     *        }
     *    }
     *
     *
     *  //在初始化文件 init.php 中注册过滤器
     *  DI()->filter = 'My_Filter';
     * ```
     *
     * @see PhalApi_Filter::check()
     *
     * @throws BadRequest 当验证失败时，请抛出此异常，以返回400
     */
    protected function filterCheck()
    {
        $filter = DI()->get('filter', 'PhalApi_Filter_None');

        if (isset($filter)) {
            if (!($filter instanceof PhalApi_Filter)) {
                throw new BadRequest(
                    T('DI()->filter should be instanceof PhalApi_Filter'));
            }

            $filter->check();
        }
    }

    /**
     * 用户身份验证
     *
     * 可由开发人员根据需要重载，此通用操作一般可以使用委托或者放置在应用接口基类
     *
     * @throws BadRequest 当验证失败时，请抛出此异常，以返回400
     */
    protected function userCheck()
    {

    }
}
