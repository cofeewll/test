<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13 0013
 * Time: 上午 11:20
 */

namespace Common\Model;


use Think\Model;

class UserMoneyModel extends Model
{
    public function addLog($uid,$type,$cmoney,$shopId,$memo){
        $before_money=M("user")->where("id=$uid")->getField("money");
        $map=array(
            "uid"=>$uid,
            "type"=>$type,
            "cmoney"=>$cmoney,
            "beforeMoney"=>$before_money,
            "afterMoney"=>$before_money+$cmoney,
            "createTime"=>time(),
            "shopId"=>$shopId,
            "memo"=>$memo
        );
        $res1=$this->add($map);
        $res2=M("user")->where("id=$uid")->setInc("money",$cmoney);
        if($res1&&$res2){
            return true;
        }else{
            return false;
        }
    }
}