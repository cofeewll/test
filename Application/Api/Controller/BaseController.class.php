<?php
namespace Api\Controller;
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
    //实例化缓存类
    public function newMemcache(){
        $mem = new \Memcache();
        if($mem->connect("127.0.0.1",11211)){
            return $mem;
        } else {
            return false;
        }
    }

    
    
    
    
}