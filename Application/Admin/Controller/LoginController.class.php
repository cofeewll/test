<?php
namespace Admin\Controller;
 
/**
 * 登录
 * @author xiegaolei
 *
 */
class LoginController extends \think\Controller
{    
    
    /**
     * 登录页
     */
    public function index(){
     
        if(is_login()){
            $this->redirect('index/index');
        }
        
        $this->display();
        
    }
    public function getCode(){
        $username=I("post.username");
        $admin=M("admin")->where(["username"=>$username])->find();
        if(!$admin){
            ajax_return_error("你输入的账号不存在");
        }
        if($admin['status']==-1){
            ajax_return_error("你的账号已被锁定，请联系超级管理员");
        }
        if($admin['status']==0){
            ajax_return_error("你的账号已被禁用,请联系超级管理员");
        }
        $type=I("post.type");
        if(!check_mobile($admin['mobile'])){
            ajax_return_error("手机号格式不正确");
        }
        getcode($admin['mobile'],$type);
    }
    
    /**
     * 登录验证
     */
    public function loginAuth(){
        if (IS_POST){
            
            //接收数据
            $data = array(
                    'username'  => I('username','','trim'),
                    'password'   =>I ('password','','trim'),
                    'verify' => I('verify','','trim')
            );
             
            if(empty($data['username'])){
                ajax_return_error('账号必填！');
            }
            
            if(empty($data['password'])){
                ajax_return_error('密码必填！');
            }
            
            if(empty($data['verify'])){
                ajax_return_error('验证码必填！');
            }
            
//            verifyCode($data['username'],3,$data['verify']);
            //账号验证
            $uid = $this->logins($data['username'], $data['password']);
            if(0 < $uid){ //登录成功
            
                ajax_return_ok(array('url'=>U("Index/index")),"登录成功！");
                 
            
            } else { //登录失败
                switch($uid) {
                    case -1: $error = '账号不存在或被禁用！'; break; //系统级别禁用
                    case -2: $error = '密码错误！'; break;
                    case -3: $error = '用户组不存在或被禁用！'; break;
                    case -4; $error='你已经被锁定,请联系超级管理员';break;
                    case -5; $error='账号已解锁，请先修改密码';break;
                    default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
                }
                ajax_return_error($error);
            }
        }
        
        
    }
    
    
    
    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function logins($username, $password){
         //用户
        $admin = M('admin')->where(array('username'=>$username))->find();
        if (empty($admin)){
            return -1;
        }
        if(empty($admin['password'])){
            return -5;
        }
        if($admin['status']==-1){
            return -4;
        }
        //角色
        $role =M('auth_group_access')
                 ->alias('a')
                 ->field('b.*')
                 ->join('wg_auth_group b on a.group_id = b.id')
                 ->where(array('uid'=>$admin['id']))
                 ->find();
            
        if (empty($role) || $role['status'] != 1) {
            return -3;
        }
    
        if(is_array($admin) && $admin['status']==1){
            /* 验证用户密码 */
            if(encrypt_pass($password) === $admin['password']){
                /* 记录登录SESSION */
                $auth = array(
                        'uid'             => $admin['id'],
                        'username'        => $admin['username'],
                        'loginTime'          => $admin['loginTime'],
                        'role'              => $role['title'],
                        'roleId'          => $role['id'],
                    'isAdmin'=>$admin['isAdmin'],
                );
    
                session('admin_auth', $auth);
                session('admin_auth_sign', data_auth_sign($auth));
                M("admin")->where(array('mobile'=>$username))->setField("wrongNum",0);
                $this->updateLogin($admin['id'],$admin['username'],$role['title']); //更新用户登录信息
                return $admin['id']; //登录成功，返回用户ID
            } else {
                if($admin['isAdmin']!=1){
                    M("admin")->where(array('mobile'=>$username))->setInc("wrongNum",1);
                    $wrong_num=M("admin")->where(['mobile'=>$username])->getField("wrongNum");
                    if($wrong_num>=2){
                        M("admin")->where(array('mobile'=>$username))->setField("status",-1);
                    }
                }
                return -2; //密码错误
            }
        } else {
            return -1; //用户不存在或被禁用
        }
    }
    
    /**
     * 退出登录
     */
    public function logout(){
        if(is_login()){
            session('admin_auth', null);
            session('admin_auth_sign', null);
            session('[destroy]');
            $this->redirect('Login/index');
        } else {
            $this->redirect('Login/index');
        }
    }
    
    
    
    /**
     * 更新用户登录信息
     * @param  integer $uid 用户ID
     */
    protected function updateLogin($uid,$username,$role){
        $data = array(
                'loginTime'   => time(),
                'loginIp'     => get_client_ip(),
        );
        M('admin')->where(array('id'=>$uid))->save($data);
        
        $data['adminId'] = $uid;
        $data['username'] = $username;
        $data['llRule'] = $role;
        $data['llAddTime'] = time();
        $data['llType'] = 1;
        $data['llIp'] = get_client_ip();
        M('login_log')->add($data);
        
    }
    
     
    public function editPwd(){
        if(IS_POST){
            $post=I("post.");
            foreach($post as $v){
                if(empty($v)){
                    ajax_return_error("请将信息填写完整");
                }
            }
            //判断该手机号是否被锁定
            $admin=M("admin")->where(["username"=>$post['username']])->find();
            if(!$admin){
                ajax_return_error("该账号不存在");
            }
            if($admin['status']==0){
                ajax_return_error("你已经被禁用");
            }
            if($admin['status']==-1){
                ajax_return_error("你已经被锁定，请联系超级管理员解锁");
            }
//            verifyCode($post['phone'],4,$post['code']);
            $res=M("admin")->where(["username"=>$post['username']])->setField("password",encrypt_pass($post['password']));
            if($res!==false){
                ajax_return_ok([],"修改成功");
            }else{
                ajax_return_error("修改失败");
            }
        }
        $this->display();
    }
    
    
    
}
