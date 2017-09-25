<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15 0015
 * Time: 上午 10:09
 */

namespace Common\Model;


use Think\Model;

class UserScoreModel extends  Model
{
    public function addLog($uid,$type,$cscore,$orderId,$memo){
        $before_money=M("user")->where("id=$uid")->getField("score");
        $map=array(
            "uid"=>$uid,
            "type"=>$type,
            "cscore"=>$cscore,
            "beforeScore"=>$before_money,
            "afterScore"=>$before_money+$cscore,
            "createTime"=>time(),
            "orderId"=>$orderId,
            "memo"=>$memo
        );
        $res1=$this->add($map);
        $res2=M("user")->where("id=$uid")->setInc("score",$cscore);
        if($res1&&$res2){
            return true;
        }else{
            return false;
        }
    }
}