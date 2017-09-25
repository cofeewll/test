<?php
namespace Shop\Controller;
 
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
        $admin=M("shop")->where(["username"=>$username])->find();
        if(!$admin){
            ajax_return_error("你输入的账号不存在");
        }
        if($admin['status']==0){
            ajax_return_error("你的账号已被禁用,请联系超级管理员");
        }
        $type=I("post.type");
        if(!check_mobile($admin['phone'])){
            ajax_return_error("手机号格式不正确");
        }
        getcode($admin['phone'],$type);
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
                    case -1: $error = '账号不存在！'; break; //系统级别禁用
                    case -2: $error = '密码错误！'; break;
                    case -3: $error = '用户组不存在或被禁用！'; break;
                    case -4; $error='账号已被禁用';break;
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
        $admin = M('shop')->where(array('username'=>$username))->find();
        if (empty($admin)){
            return -1;
        }
        if($admin['status']==0){
            return -4;
        }
    
        if(is_array($admin) && $admin['status']==1){
            /* 验证用户密码 */
            if(encrypt_pass($password) === $admin['password']){
                /* 记录登录SESSION */
                $auth = array(
                        'shopId'             => $admin['id'],
                        'username'        => $admin['username'],
                        'title'           => $admin['title'],
                );
    
                session('shop_auth', $auth);
                return $admin['id']; //登录成功，返回用户ID
            } else {
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
            session('shop_auth', null);
            session('[destroy]');
            $this->redirect('Login/index');
        } else {
            $this->redirect('Login/index');
        }
    }
    
    
    
}
