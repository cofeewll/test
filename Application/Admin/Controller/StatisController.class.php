<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/27 0027
 * Time: 上午 11:30
 */

namespace Admin\Controller;


class StatisController extends BaseController
{
    /**
     * 充值统计
     */
    public function index(){
        if(IS_POST){
            $where="1=1 and urIsPay=1";
            if($start=I("post.start",'',"trim")){
                $where.="urAddTime>=".strtotime($start);
            }
            if($end=I("post.end",'',"trim")){
                $where.="urAddTime<=".(strtotime($start)+24*3600-1);
            }
            $data=M("user_recharge")->alias("ur")->join("jj_server as s on s.uaServerId=ur.uaServerId","left")
                ->where($where)->group("ur.uaServerId")
                ->field("sum(urMoney) as urMoney,sum(urSendMoney) as urSendMoney,ur.uaServerId,s.sUserName")->select();
            foreach($data as $k=>$v){
                if(!$v['sUserName']){
                    $data[$k]['sUserName']="平台";
                }
            }
            ajax_return_ok($data);
        }else{

            $this->display();
        }
    }
    public function withdraw(){
        if(IS_POST){
            $where="1=1 and uwState=1";
            if($start=I("post.start",'',"trim")){
                $where.=" and uwAddTime>=".strtotime($start);
            }
            if($end=I("post.end",'',"trim")){
                $where.=" and uwAddTime<=".(strtotime($start)+24*3600-1);
            }
            $data=M("user_withdraw")->alias("ur")->join("jj_server as s on s.uaServerId=ur.uaServerId","left")
                ->where($where)->group("ur.uaServerId")
                ->field("sum(uwMoney) as uwMoney,ur.uaServerId,s.sUserName")->select();
            foreach($data as $k=>$v){
                if(!$v['sUserName']){
                    $data[$k]['sUserName']="平台";
                }
            }
            ajax_return_ok($data);
        }else{

            $this->display();
        }
    }
    public function server(){
        if(IS_POST){
            $where="1=1 and swState=1";
            if($start=I("post.start",'',"trim")){
                $where.=" and swAddTime>=".strtotime($start);
            }
            if($end=I("post.end",'',"trim")){
                $where.=" and swAddTime<=".(strtotime($start)+24*3600-1);
            }
            $data=M("server_withdraw")->alias("ur")->join("jj_server as s on s.uaServerId=ur.uaServerId","left")
                ->where($where)->group("ur.uaServerId")
                ->field("sum(swMoney) as uwMoney,ur.uaServerId,s.sUserName")->select();
            foreach($data as $k=>$v){
                if(!$v['sUserName']){
                    $data[$k]['sUserName']="平台";
                }
            }
            ajax_return_ok($data);
        }else{

            $this->display();
        }
    }
}