<?php
namespace Common\Model;
use Think\Model;
use Common\CommonConstant;
use Common\Util\JwtUtil;
use Common\Util\StringUtil;


/**
 * 商家数据层处理
 * @author xiegaolei
 *
 */
class ShopModel extends Model {
 	
	/**
	 * 登录验证
	 * @param unknown $name
	 * @param unknown $pass
	 */
	public function login($username, $pass,$phone,$code,$type,$channel_id) {
        //验证码是否正确
        $data = M("sms_record")->where(array("phone"=>$phone,"isUse"=>0,"type"=>5))->order("id desc")->find();
        if($data){
        	if(time() <= $data['endtime']){
                if($code != $data['code']){
                    ajax_return_error("验证码输入不正确");
                }
            }else{
                ajax_return_error("请重新获取验证码");
            }
        }else{
        	ajax_return_error("请先去获取验证码");
        }
        // 查找身份，验证身份
        $where ['username'] = $username;
        $where ['phone'] = $phone;

        $shop = $this->where ( $where )->find ();

        if (! $shop) {
            ajax_return_error( '该账号不存在');
        }
		
        if($shop['password'] != encrypt_pass($pass)){
			ajax_return_error("密码错误");
		}
        // 检测用户状态
        switch ($shop['status']) {
        	case -2:
        		ajax_return_error( '入驻申请未通过');
        		break;
        	case -1:
        		ajax_return_error( '入驻申请正在审核');
        		break;
        	case 0:
        		ajax_return_error( '账户被禁用');
        		break;
        }
        
		$map['loginTime']=time();
		if($type == 1){
			$map['andChannel'] = $channel_id;
		}
		if($type == 2){
			$map['iosChannel'] = $channel_id;
		}
		if($type){
			$map['type'] = $type;

		}
		$this->where($where)->save($map);
		$shop = $this->where($where)->field("id,phone,title,username,img")->find();
		if($shop['img']){
			$shop['img'] = "http://".$_SERVER['HTTP_HOST'].$shop['img'];
		}else{
			$default = M('config')->where(['config'=>'avatar','flag'=>1])->getField('value');
			$shop['img'] = "http://".$_SERVER['HTTP_HOST'].$default;
		}
		//更新验证码状态
		M("sms_record")->where(["id"=>$data['id']])->setField("isUse",1);
        // 数据处理和令牌获取
        $userAccessToken = $this->processDataOfLogin ( $shop ['id'] );
        
        // 添加缓存
        session ( C ( "SESSION_NAME_CUR_SHOP" ), $shop );
        
        // 返回
        return array ('userAccessToken' => $userAccessToken,'shop' => $shop );
    }

    /**
     * 处理登录的数据，以及令牌生成和返回
     * @param int $id 用户ID
     * @return string 已编码的用户访问令牌
     */
    private function processDataOfLogin($id) {
    	// 令牌生成
    	$payload['id'] = $id;
    	$payload['loginTime'] =  time ();
    	$userAccessToken = JwtUtil::encode ( $payload );
    	// 返回令牌
    	return $userAccessToken;
    }

	
}