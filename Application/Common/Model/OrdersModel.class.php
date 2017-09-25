<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13 0013
 * Time: 下午 2:18
 */

namespace Common\Model;


use Think\Model;

class OrdersModel extends Model
{
    /**
     * 退款处理--判断是否部分退货
     */
    public function partReturn($order_id){
        $count=M("order_goods")->where("orderId=$order_id and status<3")->count();
        if($count==0){
            $res=$this->setStatus($order_id,4);//交易关闭
        }else{
            $count1=M("order_goods")->where("orderId=$order_id and status=1")->count();
            if($count1>0){
                $res=$this->setStatus($order_id,2);//待收货
            }else{
                $res=$this->setStatus($order_id,3);//退款中
            }
        }
        return $res;
    }
    //改变订单状态
    public function setStatus($order_id,$status){
        return $this->where("id=$order_id")->setField("status",$status);
    }
    /**
     * 订单发货后15d未确认收货，自动确认收货
     */
    public function sure($uid=null){
        $time=time()-(15*24*3600);
        $where="status=2 and shipTime<$time";
        if($uid){
            $where.=" and uid=$uid";
        }
        $order=M("orders")->where($where)->field("id,amount")->select();
        $model=M();
        $model->startTrans();
        foreach($order as $k=>$v){
            $res=$this->countMoney($v['id'],$v['amount']);
            if(!$res){
                $model->rollback();
                return false;
            }
        }
        $model->commit();
    }
    public function countMoney($order_id,$total_fee){
        $sub_money=M("order_refund")->where("orderId=$order_id and status=1")->sum("realMoney")?:0.00;
        $res=$this->where("id=$order_id")->save(["status"=>5,"setMoney"=>$total_fee-$sub_money]);
        return $res;
    }
    /**
     * 售后24小时未处理
     */
    public function noAction(){
        $time=time()-(24*3600);
        $where="status=0 and createTime<$time and isSend=0";
        $count=M("order_refund")->where($where)->count();
        if($count>0){
            //给管理员发送通知
            $map=array(
                "title"=>"有{$count}个售后订单超时未处理",
                "content"=>"请去查看，并及时通知商家处理",
                "createTime"=>$time,
            );
            $res=M("message")->add($map);
            M("order_refund")->where("status=0 and createTime<$time and isSend=0")->setField("isSend",1);
        }
    }
    /**
     * 查看我的订单
     */
    public function myOrder($where,$left,$num){
        $data=$this->alias("o")->join("wg_shop as s on s.id=o.shopId","left")
            ->where($where)->field("title,o.status,o.id,orderSn,amount,fee,o.address,o.realname,o.phone,remark,o.createTime")
            ->order("o.id desc")->limit($left,$num)->select();
        foreach($data as $k=>$v){
            $data[$k]['address']=str_replace("|","",$v['address']);
            $order_id=$v['id'];
            $child=M("order_goods")->alias("og")->join("wg_goods as g on og.goodsId=g.id","left")
                ->where("og.orderId=$order_id")->field("name,img,og.number,og.price,og.spec,og.status,og.goodsId,og.gsId")->select();
            $total_num=0;
            foreach($child as $k_=>$v_){
                $child[$k_]['img']="http://".$_SERVER['HTTP_HOST'].$v_['img'];
                $total_num+=$v_['number'];
                if($v_['spec']){
                    $child[$k_]['specArr'] = getSpec($v_['spec']);
                }
            }
            $data[$k]['address'] = str_replace('|', '', $v['address']);
            $data[$k]['goods']=$child;
            $data[$k]['total_num']=$total_num;
        }
        return $data;
    }
    /**
     * 打印电子面单，加入记录
     */
    public function addPrintLog($shop_id,$order_id,$order_sn,$LogisticCode){
        $map=array(
            "shopId"=>$shop_id,
            "orderId"=>$order_id,
            "orderSn"=>$order_sn,
            "LogisticCode"=>$LogisticCode,
            "createTime"=>time(),
        );
        $res=M("print_log")->add($map);
    }
    /**
     * 订单变化加入消息表同时推送
     */
    public function addNotice($title,$content,$type,$send_id){
        $map=array(
            "title"=>$title,
            "content"=>$content,
            "type"=>$type,
            "createTime"=>time(),
            "updateTime"=>time(),
            "sendId"=>$send_id,
        );
        $res=M("notice")->add($map);

        if($type==1){
            $user=M("user")->find($send_id);
            $andChanelId = $user['uAndChannel'];
        }else{
            $shop=M("shop")->find($send_id);
            $andChanelId = $shop['andChannel'];
        }
        $msg = array();
        $msg['ticker'] = "唯公商城";
        $msg['title'] = $title;
        $msg['text'] = $content;
        $msg['ktype'] = 2;
        if( !empty($andChanelId) ) {
            sendAndroidListcast($andChanelId,$msg);//安卓推送
        }
        return $res;
    }
    /**
     * 半小时未付款  交易关闭
     */
    public function close($uid=null){
        $time=time()-1800;
        $where="status=0 and createTime<$time";
        if($uid){
            $where.=" and uid=$uid";
        }else{
            $where.=" and 1=1";
        }
        $res=$this->where($where)->save(["status"=>4]);
        return $res;
    }
    
}