<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/12 0012
 * Time: 下午 1:43
 */

namespace Shop\Controller;


use Common\Util\ExcelUtil;
use Common\Util\PayUtil;
use Common\Util\ShipUtil;

class OrderController extends BaseController
{
    public $db;
    public function __construct()
    {
        parent::__construct();
        $this->db=M("orders");
    }
    public function index(){
        if(!IS_POST){
            $this->ship=C('ship');
            D("Orders")->close();
            D("Orders")->sure();
            $this->display();
            return ;
        }
        $page=I("page"); // get the requested page
        $limit = I('rows'); // get how many rows we want to have into the grid
        $sidx = I('sidx'); // get index row - i.e. user click to sort
        $sord = I('sord'); // get the direction
        if(!$sidx) $sidx ="id";

        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows']: false;
        if($totalrows) {
            $limit = $totalrows;
        }

        //搜索条件
        $get=$_GET;
        $where=$this->getWhere($get);
        $count = $this->db->alias("o")->join("wg_user as u on u.id=o.uid","left")
            ->join("wg_shop as s on s.id=o.shopId","left")->where($where)->count();

        if( $count >0 ) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        if ($limit<0) $limit = 0;
        $start = $limit*$page - $limit; // do not put $limit*($page - 1)
        if ($start<0) $start = 0;

        $result=$this->db->alias("o")->join("wg_user as u on u.id=o.uid","left")
            ->join("wg_shop as s on s.id=o.shopId","left")
            ->field("o.*,u.number,s.username")->where($where)->order("$sidx $sord")->limit($start,$limit)->select();

        $total_oMoney=$this->db->alias("o")->join("wg_user as u on u.id=o.uid","left")
            ->join("wg_shop as s on s.id=o.shopId","left")->where($where)->sum("amount");
        $total_oPayMoney=$this->db->alias("o")->join("wg_user as u on u.id=o.uid","left")
            ->join("wg_shop as s on s.id=o.shopId","left")->where($where)->sum("goodsAmount");
        foreach($result as $k=>$v){
            $result[$k]['total_oMoney']=$total_oMoney;
            $result[$k]['total_oPayMoney']=$total_oPayMoney;
            $result[$k]['order_status']=$v['status'];
            $result[$k]['shipName']=C("ship")[$v['shipName']];
            $result[$k]['createTime']=date("Y-m-d H:i:s",$v['createTime']);
            if($v['commentTime']==0){
                $result[$k]['isComment']=0;
            }else{
                $result[$k]['isComment']=1;
            }
        }
        $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
        echo json_encode($responce);
    }
    public function getWhere($get){
        //搜索条件
        $sid=$this->sid;
        $where = "shopId=$sid";
        foreach($get as $k=>$v){
            if($k=='start'&&$v!=''){
                $where.=" and createTime>=".strtotime($v);
                continue;
            }
            if($k=='end'&&$v!=''){
                $where.=" and createTime<=".(strtotime($v)+24*3600-1);
                continue;
            }
            if($k=='orderSn'&&$v!=''){
                $where.=" and orderSn like '%$v%'";
                continue;
            }
            if($k=='uNumber'&&$v!=''){
                $where.=" and u.number like '%$v%'";
                continue;
            }
            if($k=='sUserName'&&$v!=''){
                $where.=" and s.username like '%$v%'";
                continue;
            }
            if($k=='payTime'&&$v!=''){
                if($v==0){
                    $where.=" and o.$k=$v";
                }else{
                    $where.=" and o.$k>0";
                }
                continue;
            }
            if($v!=''){
                $where.=" and o.$k=$v";
            }
        }
        return $where;
    }
    public function excel(){
        //搜索条件
        $get=$_GET;
        $where=$this->getWhere($get);
        $modellist = $this->db->alias("o")->join("wg_user as u on u.id=o.uid","left")
            ->join("wg_shop as s on s.id=o.shopId","left")->where($where)->field("o.*,u.number,s.username")
            ->order("id desc")->select();
        $order_status=array(1=>'待发货',2=>'待收货',3=>'待评价',4=>"售后退款/退货",5=>"已完成");
        $from=[1=>'支付宝',2=>'微信',0=>'余额支付'];
        foreach($modellist as $k=>$v){
            $modellist[$k]['status']=$order_status[$v['status']];
            $modellist[$k]['payType']=$from[$v['payType']];
            $modellist[$k]['payTime']=date("Y-m-d H:i:s",$v['payTime']);
        }
        $fileName = "唯公商城订单列表".date('Y-m-d').".xlsx";
        $cellvalue=[
            ["value"=>"订单编号","name"=>"orderSn","width"=>"20"],
            ["value"=>"用户编号","name"=>"number","width"=>"20"],
            ["value"=>"商家账号","name"=>"username","width"=>"20"],
            ["value"=>"订单状态","name"=>"status","width"=>"20"],
            ["value"=>"支付方式","name"=>"payType","width"=>"20"],
            ["value"=>"订单总额","name"=>"amount","width"=>"20"],
            ["value"=>"商品总额","name"=>"goodsAmount","width"=>"20"],
            ["value"=>"收货地址","name"=>"address","width"=>"20"],
            ["value"=>"收货人","name"=>"realname","width"=>"20"],
            ["value"=>"联系电话","name"=>"phone","width"=>"20"],
            ["value"=>"支付时间","name"=>"payTime","width"=>"20"],
        ];
        $util=new ExcelUtil();
        $objPHPExcel=$util->export(11,$modellist,"A","k",$cellvalue);
        excelDown($objPHPExcel, $fileName);
    }
    public function detail(){
        $id=I("id","","trim");
        $order=$this->db->where(["id"=>$id])->find();
        $order['shipName']=C("ship")[$order['shipName']];
        $order_goods=M("order_goods")->alias("og")
            ->join("wg_goods as g on g.id=og.goodsId")->field("og.id,name,img,og.gsId,og.number,spec,og.price,og.status")
            ->where("orderId=$id")->select();
        $this->order_goods=$order_goods;
        $this->order=$order;

        $this->display();
    }
    /**
     * 订单统计
     */
    public function statis(){
        $sid=$this->sid;
        if(IS_POST){
            //搜索条件
            $where = 'o.status in(1,2,3,5) and o.shopId='.$sid;
            $get=$_POST;
            foreach($get as $k=>$v){
                if($k=='start'&&$v!=''){
                    $where.=" and o.createTime>=".strtotime($v);
                    continue;
                }
                if($k=='end'&&$v!=''){
                    $where.=" and o.createTime<=".strtotime($v);
                    continue;
                }
                if($v!=''){
                    $where.=" and $k=$v";
                }
            }
            //按照订单支付方式统计
            $modellist = $this->db->alias("o")->where($where)->group("payType")->field("sum(amount) as money,payType")->select();
            //按照商品分类统计
            $school=M("order_goods")->alias("og")->join("wg_orders as o on o.id=og.orderId")
                ->where($where)->group("cid")->field("cid,sum(og.goodsAmount) as money")->select();
            foreach($school as $k=>$v){
                $school[$k]['school_name']=M("goods_cate")->where("id=".$v['cid'])->getField("name");
                $school_value[$k]['value']=$v['money'];
            }

            ajax_return_ok(['order'=>$modellist,'school'=>$school]);
        }else{
            $this->display();
        }
    }
    /**
     * 按年月统计
     */
    public function year(){
        //搜索条件
        $sid=$this->sid;
        $where = 'status in(1,2,3,5) and shopId='.$sid;
        $get=$_POST;
        foreach($get as $k=>$v){
            if($k=='start'&&$v!=''){
                if($get['year']!=1){
                    $where.=" and createTime>=".strtotime($v);
                }

                continue;
            }
            if($k=='end'&&$v!=''){
                if($get['year']!=1){
                    $date=explode("-",$v);
                    $thismonth = $date[1];
                    $thisyear = $date[0];
                    $endDay = $thisyear . '-' . $thismonth . '-' . date('t', strtotime($v));
                    $e_time  = strtotime($endDay);//当前月的月末时间戳
                    $where.=" and createTime<=".$e_time;
                }

                continue;
            }
            if($k=='year'){
                continue;
            }
            if($v!=''){
                $where.=" and $k=$v";
            }
        }
        if($get['year']==1){
            //按年统计
            $modellist = $this->db->where($where)->group("oYear")->field("sum(amount) as money,oYear,count(id) as total")->select();
        }else{
            //按月统计
            $modellist=$this->db->where($where)
                ->group("oYear,oMonth")->field("sum(amount) as money,oYear,oMonth,count(id) as total")->select();
        }

        ajax_return_ok(['order'=>$modellist]);
    }
    /**
     * 售后管理
     */
    public function refund(){
        if(!IS_POST){
            $this->display();
            return ;
        }
        $page=I("page"); // get the requested page
        $limit = I('rows'); // get how many rows we want to have into the grid
        $sidx = I('sidx'); // get index row - i.e. user click to sort
        $sord = I('sord'); // get the direction
        if(!$sidx) $sidx ="id";

        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows']: false;
        if($totalrows) {
            $limit = $totalrows;
        }

        //搜索条件
        $get=I("get.",'',"trim");
        $where=$this->getWhere1($get);
        $count = M("order_refund")->alias("ore")
            ->join("wg_orders as o on o.id=ore.orderId","left")
            ->join("wg_goods as g on g.id=ore.goodsId","left")
            ->where($where)->count();

        if( $count >0 ) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        if ($limit<0) $limit = 0;
        $start = $limit*$page - $limit; // do not put $limit*($page - 1)
        if ($start<0) $start = 0;

        $result=M("order_refund")->alias("ore")
            ->join("wg_orders as o on o.id=ore.orderId","left")
            ->join("wg_goods as g on g.id=ore.goodsId","left")
            ->field("ore.*,o.orderSn,g.name")->where($where)->order("$sidx $sord")->limit($start,$limit)->select();
        $time=time();
        foreach($result as $k=>$v){
            if($v['dealTime']==0){
                if(($v['createTime']+24*3600)<$time){
                    $result[$k]['warn']=1;
                }
            }else{
                $result[$k]['warn']=0;
            }

            $result[$k]['createTime']=date("Y-m-d H:i:s",$v['createTime']);

        }
        $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
        echo json_encode($responce);
    }
    /**
     * 发货
     */
    public function send(){
        $post=I("post.");
        $post['shipTime']=time();
        $post['status']=2;
        $res=$this->db->save($post);
        if($res){
            //加入消息通知
            D("Orders")->addNotice("发货通知","你有一条订单已发货",1,M("orders")->where("id=".$post['id'])->getField("uid"));
            ajax_return_ok([],"发货成功");
        }else{
            ajax_return_error("发货失败");
        }
    }
    public function getWhere1($get){
        //搜索条件
        $sid=$this->sid;
        $where = "ore.shopId=$sid and o.status>0 ";
        foreach($get as $k=>$v){
            if($k=='start'&&$v!=''){
                $where.=" and createTime>=".strtotime($v);
                continue;
            }
            if($k=='end'&&$v!=''){
                $where.=" and createTime<=".(strtotime($v)+24*3600-1);
                continue;
            }
            if($k=='orderSn'&&$v!=''){
                $where.=" and orderSn like '%$v%'";
                continue;
            }
            if($k=='uNumber'&&$v!=''){
                $where.=" and u.number like '%$v%'";
                continue;
            }
            if($v!=''){
                $where.=" and o.$k=$v";
            }
        }
        return $where;
    }
    /**
     * 售后订单详情
     */
    public function rdetail($id){
        $refund=M("order_refund")->find($id);
        $this->refund=$refund;
        $order_goods=M("order_goods")->where(["goodsId"=>$refund['goodsId'],"orderId"=>$refund['orderId']])->find();
        $goods=M("goods")->where(["id"=>$refund['goodsId']])->field("img,name")->find();
        $goods['spec']=$order_goods['spec'];
        $goods['price']=$order_goods['price'];
        $goods['number']=$order_goods['number'];
        $this->goods=$goods;
        $this->display();
    }
    /**
     * 处理退款
     */
    public function doRefund(){
        $post=I("post.");
        $order=M("order_refund")->where("id=".$post['id'])->field("orderId,shopId,refundSn,goodsId")->find();
        if(!$order['orderId']){
            ajax_return_error("系统数据错误");
        }
        $order_info=M("orders")->where("id={$order['orderId']}")->field("uid,payType,userMoney,payMoney,orderSn,paySn")->find();
        $model=M();
        $model->startTrans();
        $post['dealTime']=time();
        $res1=M("order_refund")->save($post);
        if($post['status']==1){
            //同意退款
            $uid=$order_info['uid'];
            if($post['addMoney']>0){
                //补偿金--加入用户钱包明细
                $res2=D("UserMoney")->addLog($uid,1,$post['addMoney'],$order['shopId'],"商家转赠");
            }else{
                $res2=1;
            }
            if($post['realMoney']>0){
                //执行退款操作
                //1判断支付方式
                $pay_type=$order_info['payType'];
                if($pay_type>0){
                    if($order_info['payMoney']<=$post['realMoney']){
                        $money=$order_info['payMoney'];
                    }else{
                        $money=$post['realMoney'];
                    }
                    $money1=$post['realMoney']-$order_info['payMoney'];
                    $payutil=new PayUtil();
                }
                $res5=1;$res4=1;
                switch ($pay_type){
                    case 0://余额支付
                        $res4=D("UserMoney")->addLog($uid,4,$post['realMoney'],$order['shopId'],"商品退款");
                        break;
                    case 1://支付宝支付
                        $biz=[
                            "out_trade_no"=>$order_info['paySn'],
                            "trade_no"=>$order_info['trade_no'],
                            "refund_amount"=>$money,
                            "refund_reason"=>"商品退款",
                            "out_request_no"=>$order['refundSn'],
                        ];
                        $res4=$payutil->aliapyRefund($biz);
                        if($money1>0){
                            $res5=D("UserMoney")->addLog($uid,4,$money1,$order['shopId'],"商品退款");
                        }
                        break;
                    case 2://微信支付
                        $biz=[
                            "out_trade_no"=>$order_info['paySn'],
                            "trade_no"=>$order_info['trade_no'],
                            "refund_amount"=>$money,
                            "payMoney"=>$order_info['payMoney'],
                            "out_request_no"=>$order['refundSn'],
                        ];
                        $res4=$payutil->wxRefund($biz);
                        if($money1>0){
                            $res5=D("UserMoney")->addLog($uid,4,$money1,$order['shopId'],"商品退款");
                        }
                        break;
                }
                $res3=M("order_goods")->where("orderId={$order['orderId']} and goodsId={$order['goodsId']}")
                    ->setField("status",3);
                //判断是否部分退货
                $res6=D("Orders")->partReturn($order['orderId']);
                if($res1&&$res2&&$res3&&$res4&&$res5&&($res6!==false)){
                    //加入消息通知
                    D("Orders")->addNotice("退款通知","你有一条订单商家已同意退款",1,$order['uid']);
                    $model->commit();
                    ajax_return_ok([],"处理成功");
                }else{
                    //加入消息通知
                    D("Orders")->addNotice("退款通知","你有一条订单商家已拒绝退款",1,$order['uid']);
                    $model->rollback();
                    ajax_return_error("处理失败");
                }

            }else{
                ajax_return_error("请输入有效的退款金额");
            }
        }else{
            $res2=M("orders")->where("id={$order['orderId']}")->setField("status",2);
            $res3=M("order_goods")->where("orderId={$order['orderId']} and goodsId={$order['goodsId']}")
                ->setField("status",4);
            if($res1&&($res2!==false)&&$res3){
                $model->commit();
                ajax_return_ok([],"处理成功");
            }else{
                $model->rollback();
                ajax_return_error("处理失败");
            }
        }
    }
    /**
     * 编辑订单商品规格
     */
    public function selectSpec(){
        if(IS_POST){
            $post=I("post.");
            $spec=M("goods_spec")->find($post['gsId']);
            $order_goods=M("order_goods")->find($post['id']);
            if($spec['store']<$order_goods['number']){
                ajax_return_error("该规格商品库存不足，不能更换");
            }
            $model=M();
            $model->startTrans();
            $res1=M("goods_spec")->where(["id"=>$order_goods['gsId']])->setInc("store",$order_goods['number']);
            $res2=M("goods_spec")->where(["id"=>$post['gsId']])->setDec("store",$order_goods['number']);
            $map=["gsId"=>$post['gsId'],"spec"=>$spec['key_name'],"price"=>$spec['price']];
            $res3=M("order_goods")->where(["id"=>$post['id']])->save($map);
            if($res1&&$res2&&$res3){
                $model->commit();
                ajax_return_ok([],"操作成功");
            }else{
                $model->rollback();
                ajax_return_error("操作失败");
            }
        }else{
            $id=I("id");
            $order_goods=M("order_goods")->find($id);
            $goods_spec=M("goods_spec")->where("goodsId=".$order_goods['goodsId'])->select();
            $this->spec=$goods_spec;
            $this->gsId=$order_goods['gsId'];
            $this->id=$id;
            $this->display();
        }
    }
    /**
     * 发货配置
     */
    public function config(){
        $sid=$this->sid;
        $data=M("shop_send")->where("shopId=$sid")->find();
        if(IS_POST){
            $post=I("post.");
            foreach ($post as $k=>$v){
                if(empty($v)){
                    ajax_return_error("请将信息填写完整");
                }
            }
            $post['shopId']=$sid;
            if($data){
                $res=M("shop_send")->save($post);
            }else{
                $res=M("shop_send")->add($post);
            }
            if($res){
                ajax_return_ok([],"操作成功");
            }else{
                ajax_return_error("操作失败");
            }

        }else{
            $this->data=$data;
            $this->display();
        }

    }
    /**
     * 物流追踪
     */
    public function ship($id){
        $order=M("orders")->find($id);
        $map=array(
            'ShipperCode'=>$order['shipName'],
            'LogisticCode'=>$order['shipId'],
        );
        $ShipUtil=new ShipUtil();
        $result=$ShipUtil->index(json_encode($map));
        $result = json_decode(object_array($result),true);
        $traces=$result['Traces'];
        $this->result=array_reverse($traces);
        $this->display();
    }
    /**
     * 打印面单
     */
    public function print_(){
        $post=I("post.");
        $id=$post['id'];
        $sid=$this->sid;
        $title=M("shop")->where("id=$sid")->getField("title");
        $sender=M("shop_send")->where("shopId=$sid")->field("Name,Mobile,ProvinceName,CityName,ExpAreaName,Address")->find();
        if(!$sender){
            ajax_return_error("请先去配置发货地址");
        }
        $data=M("orders")->find($id);
        //构造电子面单提交信息
        $eorder = [];
//        $eorder["CustomerName"] = "teststo";
//        $eorder["CustomerPwd"] = "teststopwd";
//        if($data['shipId']){
//            $eorder["LogisticCode"] = $data['LogisticCode'];
//        }
        $eorder["ShipperCode"] = $post['shipName'];
        $eorder["OrderCode"] = $data['orderSn'].rand(1000,9999);
        $eorder["PayType"] = 1;
        $eorder["ExpType"] = 1;
        $arr=explode("|",$data['address']);
        $receiver = [];
        $receiver["Name"] = $data['realname'];
        $receiver["Mobile"] = $data['phone'];
        $receiver["ProvinceName"] = $arr[0];
        $receiver["CityName"] = $arr[1];
        $receiver["ExpAreaName"] = $arr[2];
        $receiver["Address"] = $arr[3];

        $commodityOne = [];
        $commodityOne["GoodsName"] = $title;
        $commodity = [];
        $commodity[] = $commodityOne;

        $eorder["Sender"] = $sender;
        $eorder["Receiver"] = $receiver;
        $eorder["Commodity"] = $commodity;
        $eorder["IsReturnPrintTemplate"] = 1;
        $Ship=new ShipUtil();
        $result=$Ship->getSheet($eorder);
        trace($result);
        if($result["ResultCode"] == "100"){
            D("Orders")->addPrintLog($sid,$id,$data['orderSn'],$result['Order']['LogisticCode']);
            //发货
            $this->send_(["shipName"=>$result['Order']['ShipperCode'],"shipId"=>$result['Order']['LogisticCode'],"id"=>$id]);
            ajax_return_ok($result['PrintTemplate'],"电子面单下单/发货成功");
        }else{
            ajax_return_error("电子面单下单失败");
        }

    }
    /**
     * 面单发货
     */
    public function send_($post){
        $post['shipTime']=time();
        $post['status']=2;
        $res=$this->db->save($post);
        if($res){
            //加入消息通知
            D("Orders")->addNotice("发货通知","你有一条订单已发货",1,M("orders")->where("id=".$post['id'])->getField("uid"));
            return true;
        }else{
            return false;
        }
    }

}