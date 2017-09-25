<?php
namespace Common\Model;
use Think\Model;
use Common\CommonConstant;
use Common\Util\JwtUtil;
use Common\Util\StringUtil;


/**
 * 会员数据层处理
 * @author xiegaolei
 *
 */
class UserModel extends Model {
 	
	/**
	 * 登录验证
	 * @param unknown $name
	 * @param unknown $pass
	 */
	public function login($name, $pass,$uType,$channel_id,$type=null) {
        // 查找身份，验证身份
        $where ['phone'] = $name;

        $user = $this->where ( $where )->find ();

        if (! $user) {
            ajax_return_error( '该账号不存在');
        }
		if(empty($user['password'])){
			ajax_return_error("你的账号已被锁定，请先修改密码");
		}
        if($user['password']!=encrypt_pass($pass)){
			$this->where($where)->setInc("wrongNum",1);
			$wrong_num=$this->where($where)->getField("wrongNum");
			if($wrong_num>3){
				$this->where($where)->setField("password","");
			}
			ajax_return_error("密码错误");
		}
        // 检测用户状态
        if ($user ['status']==0) {
            ajax_return_error( '用户已经禁用');
        }
		$map['loginTime']=time();
		if($uType==1){
			$map['uAndChannel']=$channel_id;
		}
		if($uType==2){
			$map['uIosChannel']=$channel_id;
		}
		if($uType){
			$map['uType']=$uType;

		}
		$this->where($where)->save($map);
		$user=$this->where($where)->field("id,phone,number,nickname,img")->find();
		$user['img']="http://".$_SERVER['HTTP_HOST'].$user['img'];
        // 数据处理和令牌获取
        $userAccessToken = $this->processDataOfLogin ( $user ['id'] );
        
        // 添加缓存
        session ( C ( "SESSION_NAME_CUR_HOME" ), $user );
        
        // 返回
        return array ('userAccessToken' => $userAccessToken,'user' => $user );
    }
    /**
     * 处理登录的数据，以及令牌生成和返回
     * @param int $id 用户ID
     * @return string 已编码的用户访问令牌
     */
    private function processDataOfLogin($id) {
    	// 令牌生成
    	$payload['uid'] = $id;
    	$payload['loginTime'] =  time ();
    	$userAccessToken = JwtUtil::encode ( $payload );
    	// 返回令牌
    	return $userAccessToken;
    }
	
	/**
	 * 添加用户
	 */
	public function addUser($post){
		//判断手机号是否唯一
		$data=$this->where(["phone"=>$post['phone']])->find();
		if($data){
			ajax_return_error("该手机号已经被注册");
		}
		$user=$this->order("id desc")->find();
		if($user){
			$post['number']=$user['number']+1;
		}else{
			$post['number']=10001;
		}
		if($post['pid']>0){

		}else{
			if($user){
				$post['pid']=$user['id'];
			}else{
				$post['pid']=0;
			}
		}

		$post['createTime']=time();
		$post['updateTime']=time();
		$post['nickname']="wg".rand(10000,99999);
		$img=M("config")->where("id=7")->getField("value");
		$post['img']=$img;
		return $post;
	}
	/**
	 * 获取用户向上16级用户信息
	 */
	public function getParents($uid,$i=0,&$parents){

		$pid=$this->where("id=$uid")->getField("pid");
		$parent=$this->where("id=$pid")->field("id,phone,number")->find();
		if(!$parent){
			return $parents;
		}
		$parents[]=$parent;
		$i++;
		if($i>=15){
			return $parents;
		}
		return $this->getParents($pid,$i,$parents);

	}
	/**
	 * 二维码生成
	 */
	public function qcode($id){
		$user=$this->where("id=$id")->getField("img");
		include './Api/phpqrcode/phpqrcode.php';
		$value = "http://".$_SERVER['HTTP_HOST'].'/index.php/Home/Login/register?pid='.$id;//二维码内容
		$errorCorrectionLevel = 'L';//容错级别
		$matrixPointSize = 6;//生成图片大小
		//生成二维码图片
		$filename="qrcode_".$id.".png";
		\QRcode::png($value, './Uploads/qrcode/'.$filename, $errorCorrectionLevel, $matrixPointSize, 2);
		$logo = ".".$user;//准备好的logo图片
		$QR = './Uploads/qrcode/'.$filename;//已经生成的原始二维码图

		if (file_exists($logo)) {
			$QR = imagecreatefromstring(file_get_contents($QR));
			$logo = imagecreatefromstring(file_get_contents($logo));
			$QR_width = imagesx($QR);//二维码图片宽度
			$QR_height = imagesy($QR);//二维码图片高度
			$logo_width = imagesx($logo);//logo图片宽度
			file_put_contents("1.txt",$logo_width);
			$logo_height = imagesy($logo);//logo图片高度
			$logo_qr_width = $QR_width / 5;
			$scale = $logo_width/$logo_qr_width;
			$logo_qr_height = $logo_height/$scale;
			$from_width = ($QR_width - $logo_qr_width) / 2;
			//重新组合图片并调整大小
			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
				$logo_qr_height, $logo_width, $logo_height);
		}
		imagepng($QR, './Uploads/qrcode/'.$filename);
		return '/Uploads/qrcode/'.$filename;
	}
	
	
	
	
	
	
}