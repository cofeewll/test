<?php
namespace Shop\Controller;
use Common\Util\TreeUtil;
/**
 * 基类
 * @author xiegaolei
 *
 */
class BaseController extends \think\Controller
{
    // 控制器权限分块
    public $sid;

    // 默认控制器块的action
    public $entry = "index";

    public function _initialize(){
        if (is_login()==0) {
           $this->redirect('Login/index');
        }
        $shop = session('shop_auth');
        $this->sid = $shop['shopId'];
    }

    /**
     * [ok 成功ajax返回]
     * @param [type] $msg [description]
     * @param array $data [description]
     * @return [type] [description]
     */
    protected function ok($msg, $data = array()) {
        $info = array (
            'status' => 1,
            'info' => $msg );
        if (! empty ( $data )) {
            $info ['data'] = $data;
        }
        
        $this->ajaxReturn ( $info );
    }

    /**
     * [wrong 失败ajax返回]
     * @param [type] $msg [description]
     * @return [type] [description]
     */
    protected function wrong($msg) {
        $info = array (
            'status' => 0,
            'info' => $msg );
        $this->ajaxReturn ( $info );
    }
}
