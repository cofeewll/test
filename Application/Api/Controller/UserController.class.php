<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/8 0008
 * Time: 上午 10:02
 */

namespace Api\Controller;


class UserController extends CenterController
{
    /**
     * 个人信息资料展示
     */
    public function getUserInfo(){
        $uid=$this->uid;
        $data=M("user")->where("id=$uid")
            ->field("phone,number,sex,nickname,score,gold,money,img,qcode,aims,experience")->find();
        $data['img']="http://".$_SERVER['HTTP_HOST'].$data['img'];
        $data['qcode']="http://".$_SERVER['HTTP_HOST'].$data['qcode'];
        ajax_return_ok($data,"请求成功");
    }
    /**
     * 个人资料修改
     */
    public function editUserInfo(){
        $uid=$this->uid;
        $post=I("post.",'','trim');
        $res=M("user")->where("id=$uid")->save($post);
        if($res!==false){
            ajax_return_ok([],"修改成功");
        }else{
            ajax_return_error("修改失败");
        }
    }
    /**
     * 更换手机号
     */
    public function changePhone(){
        $uid=$this->uid;
        $post=I("post.");
        foreach($post as $v){
            if(empty($v)){
                ajax_return_error("请完整输入有效信息");
            }
        }
        $find=M("user")->where(["phone"=>$post['new']])->find();
        if($find){
            ajax_return_error("新手机号已被注册");
        }
        $phone=M("user")->where("id=$uid")->getField("phone");
        if($post['old']!=$phone){
            ajax_return_error("请求数据有误");
        }
        $data=M("sms_record")->where(["phone"=>$post['old'],"isUse"=>0,"type"=>7])->order("id desc")->find();
        if($data){
            if($data['endtime']>=time()){
                if($data['code']!=$post['old_code']){
                    ajax_return_error("验证码输入错误");
                }
                $new=M("sms_record")->where(["phone"=>$post['new'],"isUse"=>0,"type"=>7])->order("id desc")->find();
                if($new){
                    if($new['endtime']>=time()){
                        if($new['code']!=$post['new_code']){
                            ajax_return_error("验证码输入错误");
                        }
                        $res=M("user")->where("id=$uid")->setField("phone",$post['new']);
                        if($res){
                            M("sms_recode")->where("id=".$data['id'])->setField("isUse",1);
                            M("sms_recode")->where("id=".$new['id'])->setField("isUse",1);
                            ajax_return_ok([],"更换成功");
                        }else{
                            ajax_return_error("更换失败");
                        }
                    }else{
                        ajax_return_error("请重新获取新手机号验证码");
                    }
                }else{
                    ajax_return_error("请获取新手机号的验证码");
                }
            }else{
                ajax_return_error("请重新获取验证码");
            }
        }else{
            ajax_return_error("请获取验证码");
        }
    }
    /**
     * 添加收货地址
     */
    public function addAddr(){
        $uid=$this->uid;
        $post=I("post.");
        foreach($post as $v){
            if($v==''){
                ajax_return_error("请将信息填写完整");
            }
        }
        $post['uid']=$uid;
        $post['addTime']=time();
        $post['updateTime']=time();
        $res=M("address")->add($post);
        if($res){
            ajax_return_ok([],"添加成功");
        }else{
            ajax_return_error("添加失败");
        }
    }
    /**
     * 收货地址列表
     */
    public function address(){
        $uid=$this->uid;
        $data=M("address")->where("uid=$uid")->order("id desc")->select();
        ajax_return_ok($data,"请求成功");
    }
    /**
     * 编辑收货地址
     */
    public function editAddr(){
        $post=I("post.");
        if($post['isDefault']==1){
            M("address")->where("uid=".$this->uid)->save(["isDefault"=>0]);
        }
        $res=M("address")->save($post);
        if($res!==false){
            ajax_return_ok([],"修改成功");
        }else{
            ajax_return_error("修改失败");
        }
    }
    /**
     * 删除收货地址
     */
    public function delAddr(){
        $id=I("post.id");
        $res=M("address")->delete($id);
        if($res){
            ajax_return_ok([],"删除成功");
        }else{
            ajax_return_error("删除失败");
        }
    }
    /**
     * 修改密码
     */
    public function editPwd(){
        $phone=I("post.phone");
        $code=I("post.code");
        $password=I("post.new");
        $data=M("sms_recode")->where(["phone"=>$phone,"isUse"=>0,"type"=>2])->order("id desc")->find();
        if($data){
            if($data['endtime']>=time()){
                if($code==$data['code']){
                    $res=M("user")->where("id=".$this->uid)->setField("password",encrypt_pass($password));
                    if($res!==false){
                        M("sms_recode")->where("id=".$data['id'])->setField("isUse",1);
                        ajax_return_ok([],"修改成功");
                    }else{
                        ajax_return_error("修改失败");
                    }
                }else{
                    ajax_return_error("验证码输入错误");
                }
            }else{
                ajax_return_error("请重新获取验证码");
            }
        }else{
            ajax_return_error("请获取验证码");
        }
    }
    /**
     * 积分转赠-输入编号
     */
    public function getNumber(){
        $number=I("post.number");
        $data=M("user")->where(["number"=>$number])->field("number,phone,nickname")->find();
        if($data){
            ajax_return_ok($data,"请求成功");
        }else{
            ajax_return_error("输入的编号有误");
        }
    }
    /**
     * 积分转赠
     */
    public function sendScore(){
        $uid=$this->uid;
        $before=M("user")->find($uid);
        $number=I("post.number");
        $data=M("user")->where(["number"=>$number])->find();
        if(!$data){
            ajax_return_error("系统错误");
        }
        $score=I("post.score");
        if($score<=0){
            ajax_return_error("请输入有效的积分数量");
        }
        $memo=I("post.memo");
        $user=M("user")->find($uid);
        if($user['score']<$score){
            ajax_return_error("你的积分余额不足");
        }
        $model=M();
        $model->startTrans();
        //1扣掉该用户的积分
        $res1=M("user")->where("id=$uid")->setDec("score",$score);
        //2好友增加积分
        $res2=M("user")->where("id=".$data['id'])->setInc("score",$score);
        //3加入该用户积分明细
        $map1=array(
            "uid"=>$uid,
            "cscore"=>-$score,
            "type"=>3,
            "beforeScore"=>$before['score'],
            "afterScore"=>$before['score']-$score,
            "memo"=>$memo,
            "friendId"=>$data['id'],
            "createTime"=>time(),
        );
        $res3=M("user_score")->add($map1);
        //4加入好友积分明细
        $map2=array(
            "uid"=>$data['id'],
            "cscore"=>$score,
            "type"=>5,
            "beforeScore"=>$data['score'],
            "afterScore"=>$data['score']+$score,
            "memo"=>$memo,
            "friendId"=>$uid,
            "createTime"=>time(),
        );
        $res4=M("user_score")->add($map2);
        if($res1&&$res2&&$res3&&$res4){
            $model->commit();
            ajax_return_ok([],"转赠成功");
        }else{
            $model->rollback();
            ajax_return_error("转增失败");
        }
    }
    /**
     * 积分明细
     */
    public function score(){
        $uid=$this->uid;
        $page=I("page");
        $num=I("num");
        $left=($page-1)*$num;
        $data=M("user_score")->where("uid=$uid")->order("id desc")->field("cscore,type,createTime")->limit($left,$num)->select();
        $title=[1=>"购买商品",2=>"参与大转盘活动",3=>"赠送好友",4=>"平台赠送",5=>"好友赠送"];
        $title1=[1=>"消费获得",2=>"消费消耗",3=>"转赠消耗",4=>"赠送获得",5=>"转赠获得"];
        foreach($data as $k=>$v){
            $data[$k]['title']=$title[$v['type']];
            $data[$k]['title1']=$title1[$v['type']];
            $data[$k]['createTime']=date("Y-m-d H:i:s",$v['createTime']);
        }
        ajax_return_ok($data,"请求成功");

    }
    /**
     * 积分转赠记录
     */
    public function sendList(){
        $uid=$this->uid;
        $page=I("page");
        $num=I("num");
        $left=($page-1)*$num;
        $data=M("user_score")->alias("us")->join("wg_user as u on u.id=us.friendId")->where("uid=$uid and type in(3,5)")
            ->order("id desc")->field("u.number,cscore,createTime,type")->limit($left,$num)->select();
        foreach($data as $k=>$v){
            if($v['type']==3){
                $data[$k]['title']="转出";
            }else{
                $data[$k]['title']="转入";
            }
        }
        ajax_return_ok($data,"请求成功");
    }
    /**
     * 用户反馈
     */
    public function message(){
        $post=I("post.");
        foreach($post as $v){
            if(empty($v)){
                ajax_return_error("请将信息填写完整");
            }
        }
        $post['uid']=$this->uid;
        $post['createTime']=time();
        $res=M("feedback")->add($post);
        if($res){
            ajax_return_ok([],"提交成功");
        }else{
            ajax_return_error("提交失败");
        }
    }
    /**
     * 我的收藏
     */
    public function collect(){
        $uid=$this->uid;
        $page=I("page");
        $num=I("num");
        $left=($page-1)*$num;
        $data=M("collect")->alias("c")->join("wg_goods as g on g.id=c.goodsId")
            ->where("uid=$uid and type=1")->field("c.id,name,img,price,sellNum,stock")->limit($left,$num)->order("c.id desc")->select();
        foreach($data as $k=>$v){
            $data[$k]['img']="http://".$_SERVER['HTTP_HOST'].$v['img'];
        }
        ajax_return_ok($data,"请求成功");
    }
    /**
     * 我的收藏-批量删除
     */
    public function delCollect(){
        $uid=$this->uid;
        $ids=trim(I("post.ids"),",");
        if(empty($ids)){
            ajax_return_error("请选择要删除的商品");
        }
        $data=M("collect")->where("id in ($ids)")->select();
        foreach($data as $k=>$v){
            if($v['uid']!=$uid){
                ajax_return_error("系统错误");
            }
        }
        $res=M("collect")->delete($ids);
        if($res){
            ajax_return_ok([],"删除成功");
        }else{
            ajax_return_error("删除失败");
        }
    }
    /**
     * 我的金币
     */
    public function gold(){
        $uid=$this->uid;
        $page=I("page");
        $num=I("num");
        $left=($page-1)*$num;
        $gold=M("user")->where("id=$uid")->getField("gold");
        $data=M("wheel_record")->where("uid=$uid and amount>0")->order("id desc")
            ->field("createTime,amount")->limit($left,$num)->select();
        ajax_return_ok(["gold"=>$gold,"list"=>$data],"请求成功");
    }
    /**
     * 我的钱包
     */
    public function money(){
        $uid=$this->uid;
        $page=I("page");
        $num=I("num");
        $left=($page-1)*$num;
        $gold=M("user")->where("id=$uid")->getField("money");
        $data=M("user_money")->where("uid=$uid")->order("id desc")
            ->field("createTime,cmoney,type")->limit($left,$num)->select();
        $title=[1=>"商家赠送",2=>"平台赠送",3=>"购买商品",4=>"商品退款"];
        $title1=[1=>"商家赠送",2=>"平台赠送",3=>"消费消耗",4=>"商品退款"];
        foreach($data as $k=>$v){
            $data[$k]['title']=$title[$v['type']];
            $data[$k]['title1']=$title1[$v['type']];
        }
        ajax_return_ok(["gold"=>$gold,"list"=>$data],"请求成功");
    }
    /**
     * 我的推广页面
     */
    public function promote(){
        $uid=$this->uid;
        $data=M("user")->where("id=$uid")->field("number,phone,nickname,qcode")->find();
        $data['qcode']="http://".$_SERVER['HTTP_HOST'].$data['qcode'];
        $parents=D("User")->getParents($uid);
        $ids=array_column($parents,"id");
        array_push($ids,$uid);
        $str=join(",",$ids);
        if(count($parents)<16){
            $num=16-count($parents);
            $gold=M("user")->where("id not in($str)")->order("gold desc")->field("id,number,phone")->limit($num)->select();
            foreach($gold as $k=>$v){
                array_push($parents,$gold[$k]);
            }
        }
        $data['parents']=$parents;
        ajax_return_ok($data,"请求成功");
    }
    /**
     * 申请店铺入驻
     */
    public function apply(){
        $post=I("post.");
        foreach($post as $k){
            if(empty($k)){
                ajax_return_error("请将信息填写完整");
            }
        }
        $post['fromId']=$this->uid;
        $post['regTime']=time();
        $post['updateTime']=time();
        $post['phone']=M("user")->where("id=".$this->uid)->getField("phone");
        $res=M("shop")->add($post);
        if($res){
            M("shop")->where("id=$res")->setField("username",$res+10000);
            ajax_return_ok([],"申请成功");
        }else{
            ajax_return_error("申请失败");
        }
    }
    /**
     * 消息列表
     */
    public function notice(){
        $uid=$this->uid;
        $data=M("notice")->where("type<2 and status=1")->order("id desc")->select();
        foreach($data as $k=>$v){
            $content=str_replace('src="/Uploads','src="http://'.$_SERVER['HTTP_HOST'].'/Uploads',$v['content']);
            $content=str_replace('<img','<img style="width:100%;" ',$content);
            $data[$k]['content']=$content;
            $find=M("notice_read")->where("nid={$v['id']} and uid=$uid and type=1")->find();
            if($find){
                $data[$k]['is_read']=1;
            }else{
                $data[$k]['is_read']=0;
            }
            $data[$k]['brief']=strip_tags(htmlspecialchars_decode($v['content']));
        }
        ajax_return_ok($data,"请求成功");
    }
    /**
     * 消息由未读变成已读
     */
    public function sysRead(){
        $id=I("id");
        $uid=$this->uid;
        if(M("notice_read")->where("nid=$id and uid=$uid and type=1")->find()){
            ajax_return_ok([],"请求成功");
        }else{
            $res=M("notice_read")->add(["nid"=>$id,"uid"=>$uid,"createTime"=>time(),"type"=>1]);
            if($res){
                ajax_return_ok([],"修改成功");
            }else{
                ajax_return_error("修改失败");
            }
        }

    }
    /**
     * 判断是否有未读消息
     */
    public function hasNoRead(){
        $uid=$this->uid;
        $arr1=M("notice")->where("type<2 and status=1")->field("id")->select();
        $arr1=array_column($arr1,"id");
        $arr2=M("notice_read")->where("uid=$uid and type=1")->field("nid")->select();
        $arr2=array_column($arr2,"id");
        $res=array_diff($arr1,$arr2);
        if($res){
            ajax_return_ok(["has_no_read"=>1]);
        }else{
            ajax_return_ok(["has_no_read"=>0]);
        }
    }
    /**
     * 我的支付记录
     */
    public function payLog(){
        $uid=$this->uid;
        $page=I("page");
        $num=I("num");
        $left=($page-1)*$num;
        $data=M("orders")->where("uid=$uid and payTime>0")->field("createTime,payType,userMoney,payMoney")
            ->order("id desc")->limit($left,$num)->select();
        ajax_return_ok($data,"请求成功");
    }
}