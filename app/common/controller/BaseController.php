<?php
/**
 * *
 *  * Created by PhpStorm.
 *  * User: shibing
 *  * ============================================================================
 *  * 版权所有 2018-2020 南阳市微企胜网络科技有限公司，并保留所有权利。
 *  * 网站地址: http://www.weiqisheng.cn；
 *  * ----------------------------------------------------------------------------
 *  * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 *  * 使用；不允许对程序代码以任何形式任何目的的再发布。
 *  * ============================================================================
 *  * Date: 2019/9/19 下午3:12
 *  * Time: $time
 *
 */

declare (strict_types=1);

namespace app\common\controller;

use app\common\library\traits\Jump;
use think\App;
use think\exception\ValidateException;
use think\facade\View;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    protected $view;

    use Jump;

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
        $this->view = $this->app->view;
        // 控制器初始化
        $this->_initialize();
    }

    protected function assign($name, $value = null)
    {
        View::assign($name, $value);
    }

    // 初始化
    protected function _initialize()
    {
    }

    /**
     * 验证数据
     * @access protected
     * @param array $data 数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                list($validate, $scene) = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

}