<?php
namespace Home\Controller;
use Think\Controller;

/**
* 商家登录接口
*/
class LoginController extends Controller
{
	public function register(){
        $pid=I("pid");
        $this->pid=$pid?:0;
        $this->display();
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
        }


    }
    public function article(){
        $data=M("article")->where("id=1")->find();
        $content=str_replace('src="/Uploads','src="http://'.$_SERVER['HTTP_HOST'].'/Uploads',$data['context']);
        $data['context']=htmlspecialchars_decode($content);
        $this->data=$data;
        $this->display();
    }
    public function doAction(){
        $post=I("post.",'','trim');
        $data=M("sms_record")->where(array("phone"=>$post['phone'],"isUse"=>0,"type"=>1))->order("id desc")->find();
        if($data){
            if(time()<=$data['endtime']){
                if($post['code']==$data['code']){
                    if($password=$post['password']){
                        if(preg_match("/^[0-9a-zA-Z]{6,16}$/",$password)){
                            $map['password']=encrypt_pass($password);
                            $map['phone']=$post['phone'];
                            $map['pid']=$post['pid'];
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
    public function down(){
        $this->display();
    }
}