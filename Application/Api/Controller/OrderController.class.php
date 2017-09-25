<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15 0015
 * Time: 上午 9:15
 */

namespace Api\Controller;


use Common\Util\ShipUtil;

class OrderController extends CenterController
{
    public function myOrder(){
        $uid=$this->uid;
        D("Orders")->close($uid);
        D("Orders")->sure($uid);
        $type=I("type");
        $page=I("page")?:1;
        $num=I("num")?:10;
        $left=($page-1)*$num;
        $where="uid=$uid";
        switch ($type){
            case 1://全部
                break;
            case 2://待付款
                $where.=" and o.status=0";
                break;
            case 3://待发货
                $where.=" and o.status=1";
                break;
            case 4://已发货
                $where.=" and o.status=2";
                break;
            case 5://已完成
                $where.=" and o.status=5";
                break;
        }
        $data=D("Orders")->myOrder($where,$left,$num);
        ajax_return_ok($data,"请求成功");
    }
    /**
     * 取消订单
     */
    public function cancelOrder(){
        $id=I("post.id");
        $res=M("orders")->where("id=$id")->setField("status",4);
        if($res!==false){
            ajax_return_ok([],"操作成功");
        }else{
            ajax_return_error("操作失败");
        }
    }
    /**
     * 获取退款原因
     */
    public function reason(){
        $data=M("config")->where("id=9")->getField("value");
        $data=explode("|",trim($data,"|"));
        ajax_return_ok($data);
    }
    /**
     * 申请售后
     */
    public function refund(){
        $post=I("post.");
        $order_id=$post['orderId'];
        $goods_id=$post['goodsId'];
        $gsId=$post['gsId'];
        $order_goods=M("order_goods")->where("orderId=$order_id and goodsId=$goods_id and gsId=$gsId")
            ->find();
        $post['spec_key_name']=$order_goods['spec'];
        $post['refundSn']="R".date("YmdHis").$this->uid;
        $post['uid']=$this->uid;
        $post['createTime']=time();
        $post['number']=$order_goods['number'];
        $post['shopId']=M("orders")->where("id=$order_id")->getField("shopId");
        $res=M("order_refund")->add($post);
        if($res){
            //加入消息通知
            D("Orders")->addNotice("售后通知","你有一条售后订单需要处理",2,$post['shopId']);
            ajax_return_ok([],"申请成功");
        }else{
            ajax_return_error("申请失败");
        }
    }
    /**
     * 确认收货
     */
    public function sure(){
        $order_id=I("orderId");
        //1改变订单状态
        $model=M();
        $model->startTrans();
        $res1=D("Orders")->setStatus($order_id,5);
        //2送积分
        $order=M("orders")->find($order_id);
        if($order['uid']!=$this->uid){
            ajax_return_error("系统错误");
        }
        $order_goods=M("order_goods")->where("orderId=$order_id and status=1")->select();
        $total_fee=0.00;
        foreach($order_goods as $k=>$v){
            $total_fee+=$v['price']*$v['number'];
        }
        $bl=M("config")->where("id=11")->getField("value");
        $res2=D("UserScore")->addLog($order['uid'],1,round($total_fee*$bl,2),$order_id,"购买商品");
        if($res1&&$res2){
            $model->commit();
            ajax_return_ok([],"操作成功");
        }else{
            $model->rollback();
            ajax_return_error("操作失败");
        }
    }
    /**
     * 售后列表
     */
    public function refundList(){
        $uid=$this->uid;
        $page=I("page")?:1;
        $num=I("num")?:10;
        $left=($page-1)*$num;
        $data=M("order_refund")->alias("re")->join("wg_orders as o on o.id=re.orderId","left")
            ->join("wg_goods as g on g.id=re.goodsId","left")
            ->field("re.*,o.orderSn,o.createTime as addTime,name,g.img")
            ->where("re.uid=$uid")->order("id desc")->limit($left,$num)->select();
        foreach($data as $k=>$v){
            $data[$k]['img']="http://".$_SERVER['HTTP_HOST'].$v['img'];
        }
        ajax_return_ok($data,"请求成功");
    }
    /**
     * 退款撤回
     */
    public function cancel(){
        $id=I("post.id");
        $model=M();
        $model->startTrans();
        //1改变退款状态
        $res1=M("order_refund")->where("id=$id")->setField("status",-1);
        //2改变订单详情表
        $order_refund=M("order_refund")->where("id=$id")->find();
        $map=array(
            "orderId"=>$order_refund['orderId'],
            "goodsId"=>$order_refund['goodsId'],
            "gsId"=>$order_refund['gsId'],
        );
        $res2=M("order_goods")->where($map)->setField("status",1);
        //3改变订单状态
        $res3=M("orders")->where("id=".$order_refund['orderId'])->setField("status",2);
        if($res1&&$res2&&($res3!==false)){
            $model->commit();
            ajax_return_ok([],"操作成功");
        }else{
            $model->rollback();
            ajax_return_error("操作失败");
        }
    }
    /**
     * 查看物流
     */
    public function ship(){
        $id=I("post.id");
        $order=M("orders")->field("createTime,shipId,shipName")->find($id);
        $map=array(
            'ShipperCode'=>$order['shipName'],
            'LogisticCode'=>$order['shipId'],
        );
        $order['shipName']=C("ship")[$order['shipName']];
        $ShipUtil=new ShipUtil();
        $result=$ShipUtil->index(json_encode($map));
        $result = json_decode(object_array($result),true);
        $order['traces']=array_reverse($result['Traces']);
        ajax_return_ok($order,"请求成功");
    }
    /**
     * 发表评价
     */
    public function comment(){
        $post=I("post.");
        $post['shopId']=M("orders")->where("id=".$post['orderId'])->getField("shopId");
        $post['spec_key_name']=M("order_goods")
            ->where(["orderId"=>$post['orderId'],"goodsId"=>$post['goodsId'],"gsId"=>$post['gsId']])->getField("spec");
        require('./Api/badword.src.php');
        $badword1 = array_combine($badword,array_fill(0,count($badword),'*'));
        $post['context'] = strtr($post['context'], $badword1);
        $post['uid']=$this->uid;
        $post['createTime']=time();
        $res=M("order_comment")->add($post);
        if($res){
            ajax_return_ok([],"评价成功");
        }else{
            ajax_return_error("评价失败");
        }
    }
    /**
     * 删除订单
     */
    public function delOrder(){
        $id=I("post.id");
        $uid=M("orders")->where("id=$id")->getField("uid");
        if($uid!=$this->uid){
            ajax_return_error("系统错误");
        }
        $res=M("orders")->where("id=$id")->setField("isDel",1);
        if($res){
            ajax_return_ok([],"删除成功");
        }else{
            ajax_return_error("删除失败");
        }
    }
}