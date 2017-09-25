<?php
namespace ShopApi\Controller;

/**
* 商家登录接口
*/
class LoginController extends BaseController
{
	/**
	 * [login 登录]
	 * @return [type] [description]
	 */
	public function login(){
		if(!IS_POST) ajax_return_error('请求错误');
		$username = I('username','','trim');
		$password = I('password','','trim');
		$phone = I('phone','','trim');
		$code = I('code','','trim');
		$type = I("type",'','intval');
        $channel_id = I("channel_id",'','trim');
        if( empty($username) ){
        	ajax_return_error("请输入商家ID");
        }
        if( empty($password) ){
        	ajax_return_error("请输入登录密码");
        }
        if( empty($phone) ){
        	ajax_return_error("请输入手机号");
        }
        if( empty($code) ){
        	ajax_return_error("请输入手机验证码");
        }
        // 登录验证并获取包含访问令牌的用户
        $result = D('Shop')->login ( $username, $password,$phone,$code,$type,$channel_id );
        $data = array ('userAccessToken' => $result ['userAccessToken'],'shop' => $result['shop'] );
        ajax_return_ok($data,'登录成功');
	}

	/**
	 * [getCode 发送验证码]
	 * @return [type] [description]
	 */
	public function getCode(){
		$phone=I("post.phone",'','trim');
        if(empty($phone)){
            ajax_return_error("手机号不能为空");
        }
        //1判断手机号是否存在
        $shop = M("Shop")->where(["phone"=>$phone])->find();
        if(!$shop){
            ajax_return_error("账户信息不存在");
        }
        if( $shop['status']!=1 ){
            ajax_return_error("账户已被禁用或正在审核");
        }
        getcode($phone,5);
	}
}