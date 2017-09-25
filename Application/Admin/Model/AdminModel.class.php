<?php
namespace Admin\Model;
use Think\Model;

/**
* 管理员模型
*/
class AdminModel extends BaseModel{

	/* 自动验证规则 */
    protected $_validate = array(
        array('username', 'require', '登录账户不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('password', 'checkPwd', '登录密码不能为空', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
        array('password', '6,16', '登录密码长度必须在6-16位之间', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
        array('mobile', 'require', '用户手机号不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('mobile', 'checkPhone', '手机号格式不正确', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
        array('mobile', '', '该手机号已存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('username', '', '该账户名称已存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('email', 'checkEmail', '邮箱格式不正确', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
    	array('isAdmin','getVal',self::MODEL_BOTH,'callback'),
    	array('password','getPwd',self::MODEL_BOTH,'callback'),
        array('regTime', NOW_TIME, self::MODEL_INSERT),
        array('regIp', 'getIp', self::MODEL_INSERT,'callback'),
        array('updateTime', NOW_TIME, self::MODEL_BOTH),
    );

    protected function getVal(){
    	if(intval($_POST['group'])==1){
    		return 1;
    	}else{
    		return 0;
    	}
    }
    
	
	/**
	 * 用户登录认证
	 * @param  string  $username 用户名
	 * @param  string  $password 用户密码
	 * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
	 * @return integer           登录成功-用户ID，登录失败-错误编号
	 */
	public function login($username, $password, $type = 1){
		$map = array();
		switch ($type) {
			case 1:
				$map['username'] = $username;
				break;
			case 2:
				$map['email'] = $username;
				break;
			case 3:
				$map['mobile'] = $username;
				break;
			case 4:
				$map['id'] = $username;
				break;
			default:
				return 0; //参数错误
		}

		/* 获取用户数据 */
		$user = $this->where($map)->find();

		/* 获取用户组数据 */
		$group = $this->getUserGroup($user['id']);
		 
		if (empty($group) || $group['status'] != 1) {
			return -3;
		}


		if(is_array($user) && $user['status']){
			/* 验证用户密码 */
			if(think_md5($password, C('UC_AUTH_KEY')) === $user['password']){
				/* 记录登录SESSION和COOKIES */
                $auth = array(
                    'uid'             => $user['id'],
                    'username'        => $user['username'],
					'unioncode'       => $user['unioncode'],
                    'loginTime'		  => $user['loginTime'],
                    'group'			  => $group['title'],
                    'groupId'		  => $group['id'],
					'rules'		      => $group['rules'],
                );

                session('user_auth', $auth);
                session('user_auth_sign', data_auth_sign($auth));
				$this->updateLogin($user['id'],$user['username'],$group['title']); //更新用户登录信息
				return $user['id']; //登录成功，返回用户ID
			} else {
				return -2; //密码错误
			}
		} else {
			return -1; //用户不存在或被禁用
		}
	}

	/**
	 * [getUserGroup 获取用户所属用户组信息]
	 * @param  [type] $uid [用户id]
	 * @return [type]      [description]
	 */
	public function getUserGroup($uid){
		$groupId = M('auth_group_access')->where(array('uid'=>$uid))->getField('group_id');
		if (empty($groupId)) {
			return $groupId;
		}else{
			$group = M('auth_group')->where(array('id'=>$groupId))->find();
			return $group;
		}
	}


	/**
	 * 更新用户登录信息
	 * @param  integer $uid 用户ID
	 */
	protected function updateLogin($uid,$username,$role){
		$data = array(
			'id'              => $uid,
			'loginTime' => NOW_TIME,
			'loginIp'   => get_client_ip(1),
		);
		$this->save($data);

		$data1 = array(
			'loginTime' => NOW_TIME,
			'loginIp'   => get_client_ip(1),
		);
		$data1['uid'] = $uid;
		$data1['username'] = $username;
		$data1['roles'] = $role;
		M('login_log')->add($data1);
	}

}