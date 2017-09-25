<?php
namespace Shop\Controller;
 

class IndexController extends BaseController {
/**
    * 系统主页
    */
    public function index(){
        $this->title=$_SESSION['shop_admin']['shop_auth']['title'];
        
        $this->display();
    }
    public function defaultIndex(){
        $sid=$this->sid;
        D("Orders")->sure();
         //统计订单总收入
        $profit=M("orders")->where("status in(1,2,3,5) and shopId=$sid")->sum("amount");
        $this->profit=number_format($profit,2);
        $start=strtotime(date("Y-m-d"));
        $end=$start+24*3600-1;
        $today_profit=M("orders")->where("status in(1,2,3,5) and shopId=$sid and createTime>=$start and createTime<=$end")->sum("amount");
        $this->today_profit=number_format($today_profit,2);
        //订单数量
        $order_num=M("orders")->where("shopId=$sid and status in(1,2,3,5)")->count();
        $this->order_num=number_format($order_num);
        $today_order_num=M("orders")->where("createTime>=$start and createTime<=$end and shopId=$sid and status in(1,2,3,5)")->count();
        $this->today_order_num=number_format($today_order_num);

        //商品数量
        $user=M("goods")->where("shopId=$sid")->count();
        $this->user_num=number_format($user);
        $today_user=M("goods")->where("createTime>=$start and createTime<=$end and shopId=$sid")->count();
        $this->today_user_num=number_format($today_user);
        //活跃用户
        $active_user=M("shop_withdraw")->where("shopId=$sid and status=1")->sum("amount");
        $this->active_user=number_format($active_user,2);
        $bl=round($active_user/$profit,2)*100;
        $this->bl=$bl;

        //计算本月订单量
        $year=date("Y");
        $month=date("m");
        $date=date("d");
        $arr=[];$money=[];
        for($i=1;$i<=$date;$i++){
            $count=M("orders")->where("oYear=$year and oMonth=$month and oDate=$i and shopId=$sid and status in(1,2,3,5)")->count("id");
            $money_=M("orders")->where("oYear=$year and oMonth=$month and oDate=$i and shopId=$sid and status in(1,2,3,5)")->sum("amount");
            $arr[$i-1]=$count?:0;
            $money[$i-1]=$money_?:0;
        }

        $this->arr=json_encode($arr);
        $this->money=json_encode($money);
        $this->year=$year;
        $this->month=$month;
        $this->date=$date;
        // //用户充值
        // $this->recharge=M("user_recharge")->alias("ur")->join("jj_user as u on u.uId=ur.uId")->where("urIsPay=1")
        //     ->field("ur.*,u.uUserName")->order("urId desc")->limit(8)->select();
        // $this->log=M("login_log")->order("llId desc")->limit(10)->select();
        
        $this->display('default');
    }

    
}