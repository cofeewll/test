<?php
namespace Api\Controller;
use Think\Controller;
use Common\Util\AuthUtil;
use Common\Util\ArrayUtil;

/**
 * 用户登录退出
 */
class LoginController extends BaseController {
    /**
     * 登录
     */
    public function login() {
        $name = I ( 'phone', '' ,'trim');
        $pass = I ( 'password', '' ,'trim');
        $uType=I("uType",'','trim');
        $channel_id=I("channel_id",'','trim');
        if (empty($name)) {
            ajax_return_error('请填写手机号！');
        }
        if (empty($pass)){
            ajax_return_error('请填写密码！');
        }
        // 登录验证并获取包含访问令牌的用户
        $result = D('User')->login ( $name, $pass,$uType,$channel_id );
        $data = array ('userAccessToken' => $result ['userAccessToken'],'user' => $result['user'] );
        M("user")->where(["phone"=>$name])->setField("wrongNum",0);
        ajax_return_ok($data,'登录成功');
    }

    /**
     * 登出
     * 已知bug：登出的附加操作依赖session中的用户缓存，而logout方法自身并不提供用户缓存，因此这并不总是有效。
     */
    public function logout() {
        // 当前用户缓存删除
        session ( C( "SESSION_NAME_CUR_HOME" ), null );

        ajax_return_ok();
    }
    /**
     * 获取短信验证码
     */
    public function getCode(){
        $phone=I("post.phone",'','trim');
        if(empty($phone)){
            ajax_return_error("手机号不能为空");
        }
        $type=I("type")?:0;
        switch($type){
            case 1://注册获取验证码
                //1判断手机号是否注册
                $user=M("user")->where(["phone"=>$phone])->find();
                if($user){
                    ajax_return_error("该手机号已经注册");
                }
                //2判断今天注册获取验证码次数
                $start=strtotime(date("Y-m-d"));
                $end=$start+24*3600-1;
                $count=M("sms_record")->where("phone='$phone' and addtime>=$start and addtime<=$end and type=1")->count();
                if($count>3){
                    ajax_return_error("你今天已注册三次，请明天再试");
                }
                getcode($phone,1);
                break;
            case 2://忘记密码
                //1判断手机号是否存在
                $user=M("user")->where(["phone"=>$phone])->find();
                if(!$user){
                    ajax_return_error("该手机号不存在");
                }
                if(!$user['status']){
                    ajax_return_error("该手机号已被禁用");
                }
                getcode($phone,2);
                break;
            case 3://更换手机号  输入原来手机号获取验证码
                getcode($phone,7);
                break;
            case 4://更换手机号，输入新的手机号获取验证码
                //1判断新手机号系统是否存在
                $user=M("user")->where(["phone"=>$phone])->find();
                if($user){
                    ajax_return_error("该手机号已经注册");
                }
                getcode($phone,7);
                break;
            case 5://修改密码
                getcode($phone,2);
                break;
        }


    }
    /**
     * 注册
     */
    public function register(){
        $post=I("post.",'','trim');
        $data=M("sms_record")->where(array("phone"=>$post['phone'],"isUse"=>0,"type"=>1))->order("id desc")->find();
        if($data){
            if(time()<=$data['endtime']){
                if($post['code']==$data['code']){
                    if($password=$post['password']){
                        if(preg_match("/^[0-9a-zA-Z]{6,16}$/",$password)){
                            $map['password']=encrypt_pass($password);
                            $map['phone']=$post['phone'];
                            $data1=D("User")->addUser($map);
                            $res=M("user")->add($data1);
                            if($res){
                                M("user")->where("id=$res")->setField("qcode",D("User")->qcode($res));
                                M("sms_record")->where(["id"=>$data['id']])->setField("isUse",1);
                                ajax_return_ok([],"注册成功");
                            }else{
                                ajax_return_error("注册失败");
                            }
                        }else{
                            ajax_return_error("请输入由数字和字母组成的6-16位的密码");
                        }
                    }else{
                        ajax_return_error("密码不能为空");
                    }
                }else{
                    ajax_return_error("验证码输入不正确");
                }
            }else{
                ajax_return_error("请重新获取验证码");
            }
        }else{
            ajax_return_error("请先去获取验证码");
        }
    }
    /**
     * 忘记密码
     */
    public function forget(){
        $phone=I("post.phone",'','trim');
        if($user=M("user")->where(array("phone"=>$phone))->find()){
            if(!$user['status']){
                ajax_return_error("该手机号已被禁用");
            }
        }else{
            ajax_return_error("该手机号暂未注册");
        }
        $code=I("post.code",'','trim');
        $password=I("post.password",'','trim');
        $data=M("sms_record")->where(array("phone"=>$phone,"isUse"=>0,"type"=>2))->order("id desc")->find();
        if($data){
            if(time()<=$data['endtime']){
                if($code==$data['code']){
                    //判断一级密码
                    $map=array();
                    if($password){
                        if(preg_match("/^[0-9a-zA-Z]{6,16}$/",$password)){
                            $map['password']=encrypt_pass($password);
                        }else{
                            ajax_return_error("请输入由数字和字母组成的6-16位的密码");
                        }

                    }else{
                        ajax_return_error("密码输入不能为空");
                    }
                    $res=M("user")->where(array("phone"=>$phone))->save($map);
                    if($res===false){
                        M("sms_record")->where(["id"=>$data['id']])->setField("isUse",1);
                        ajax_return_error("系统开小差了，请稍后再试~");
                    }
                    ajax_return_ok("","设置成功");

                }else{
                    ajax_return_error("验证码输入错误");
                }
            }else{
                ajax_return_error("请重新获取验证码");
            }
        }else{
            ajax_return_error("请先获取验证码");
        }
    }
    


}

