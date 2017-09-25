<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/14 0014
 * Time: 上午 9:20
 */

namespace Api\Controller;


use Common\Util\AuthUtil;

class CenterController extends BaseController
{
    public $uid;
    public function __construct()
    {
        parent::__construct();
        //用户身份验证
        $result = AuthUtil::checkIdentity();
        if (!$result['status']){
            ajax_return_error(null,$result['code']);
        }
        $user=session(C("SESSION_NAME_CUR_HOME"));
        $this->uid=$user['id'];
    }
}