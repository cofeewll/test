<?php
namespace ShopApi\Controller;
use Think\Controller;
use Common\Util\AuthUtil;
use Common\CommonConstant;

/**
 * 接口基类
 *
 */
class BaseController extends Controller {
  
    public function _initialize(){
         
        //接口签名验证
        $result = AuthUtil::checkSign();
        if (!$result['status']){
    
            ajax_return_error(null,$result['code']);
        }
    }
    
}