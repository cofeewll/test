<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7 0007
 * Time: 上午 9:00
 */

namespace Common\Model;


use Think\Model;

class ShopModel extends Model
{
    /**
     * 获取商家的收入
     */
    public function getMoney($sid){
        $order=M("orders")->where("shopId=$sid")->sum("setMoney");//订单总收入
        $withdraw=M("shop_withdraw")->where("shopId=$sid and status<2")->sum("amount");
        $compen=M("user_money")->where("shopId=$sid and type=1")->sum("cmoney");
        return $order-$withdraw-$compen;
    }
}