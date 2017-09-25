<?php
namespace Admin\Controller;
 

class IndexController extends BaseController {
/**
    * 系统主页
    */
    public function index(){
        D("Orders")->noAction();
        $this->username=$_SESSION['admin_auth']['username'];
        $this->rolename=M('auth_group')->where(array('id'=>$_SESSION['admin_auth']['roleId']))->getfield('title');
        $this->message_count=M("message")->where("readTime=0")->count();
        $this->display();
    }
    public function defaultIndex(){
         D("Orders")->sure();
         //统计订单总收入
         $profit=M("orders")->where("status in(1,2,3,5)")->sum("amount");
         $this->profit=number_format($profit,2);
         $start=strtotime(date("Y-m-d"));
         $end=$start+24*3600-1;
         $today_profit=M("orders")->where("status in(1,2,3,5) and createTime>=$start and createTime<=$end")->sum("amount");
         $this->today_profit=number_format($today_profit,2);
         //订单数量
         $order_num=M("orders")->where("status in(1,2,3,5)")->count();
         $this->order_num=number_format($order_num);
         $today_order_num=M("orders")->where("createTime>=$start and createTime<=$end and status in(1,2,3,5)")->count();
         $this->today_order_num=number_format($today_order_num);

         //用户数量
         $user=M("user")->count();
         $this->user_num=number_format($user);
         $today_user=M("user")->where("createTime>=$start and createTime<=$end")->count();
         $this->today_user_num=number_format($today_user);
         //活跃用户
         $month_start=strtotime("-1 month");
         $active_user=M("user")->where("loginTime>=$month_start and loginTime<=$end")->count();
         $this->active_user=number_format($active_user);
         $bl=round($active_user/$user,2)*100;
         $this->bl=$bl;

         //计算本月订单量
         $year=date("Y");
         $month=date("m");
         $date=date("d");
         $arr=[];$money=[];
         for($i=1;$i<=$date;$i++){
             $count=M("orders")->where("oYear=$year and oMonth=$month and oDate=$i and status in(1,2,3,5)")->count("id");
             $money_=M("orders")->where("oYear=$year and oMonth=$month and oDate=$i and status in(1,2,3,5)")->sum("amount");
             $arr[$i-1]=$count?:0;
             $money[$i-1]=$money_?:0;
         }

         $this->arr=json_encode($arr);
         $this->money=json_encode($money);
         $this->year=$year;
         $this->month=$month;
         $this->date=$date;
         //
         $this->recharge=M("feedback")->order("id desc")->limit(8)->select();
         $this->log=M("login_log")->order("id desc")->limit(10)->select();
        
        $this->display('default');
    }

    
}