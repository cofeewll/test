<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/14 0014
 * Time: 上午 9:20
 */

namespace ShopApi\Controller;


use Common\Util\AuthUtil;

class CenterController extends BaseController
{
    public $sid;
    public function __construct()
    {
        parent::__construct();
        //用户身份验证
        $result = AuthUtil::checkShop();
        if (!$result['status']){
            ajax_return_error(null,$result['code']);
        }
        $shop = session(C("SESSION_NAME_CUR_SHOP"));
        $this->sid=$shop['id'];
    }
}