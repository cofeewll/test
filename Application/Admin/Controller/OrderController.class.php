<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/12 0012
 * Time: 下午 1:43
 */

namespace Admin\Controller;


use Common\Util\ExcelUtil;
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
        $get=I("get.",'',"trim");
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
            ->field("o.*,u.number,s.title")->where($where)->order("$sidx $sord")->limit($start,$limit)->select();

        $total_oMoney=$this->db->alias("o")->join("wg_user as u on u.id=o.uid","left")
            ->join("wg_shop as s on s.id=o.shopId","left")->where($where)->sum("amount");
        $total_oPayMoney=$this->db->alias("o")->join("wg_user as u on u.id=o.uid","left")
            ->join("wg_shop as s on s.id=o.shopId","left")->where($where)->sum("goodsAmount");
        foreach($result as $k=>$v){
            $result[$k]['total_oMoney']=$total_oMoney;
            $result[$k]['total_oPayMoney']=$total_oPayMoney;
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
        $where = '1=1';
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
                $where.=" and s.title like '%$v%'";
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
            $modellist[$k]['payTime']=$v['payTime']?date("Y-m-d H:i:s",$v['payTime']):"";
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
            ->join("wg_goods as g on g.id=og.goodsId")->field("g.*,og.gsId,og.number,og.spec,og.price as og_price,og.status")
            ->where("orderId=$id")->select();
        $this->order_goods=$order_goods;
        $this->order=$order;

        $this->display();
    }
    /**
     * 订单统计
     */
    public function statis(){
        if(IS_POST){
            //搜索条件
            $where = 'o.status in(1,2,3,5)';
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
        $where = 'status in(1,2,3,5)';
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
        $get=$_GET;
        $where=$this->getWhere1($get);
        $count = M("order_refund")->alias("ore")
            ->join("wg_orders as o on o.id=ore.orderId","left")
            ->join("wg_goods as g on g.id=ore.goodsId","left")
            ->join("wg_shop as s on s.id=ore.shopId","left")->where($where)->count();

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
            ->join("wg_shop as s on s.id=ore.shopId","left")
            ->field("ore.*,o.orderSn,s.title,g.name")->where($where)->order("$sidx $sord")->limit($start,$limit)->select();
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
    public function getWhere1($get){
        //搜索条件
        $where = '1=1 ';
        foreach($get as $k=>$v){
            if($k=='start'&&$v!=''){
                $where.=" and ore.createTime>=".strtotime($v);
                continue;
            }
            if($k=='end'&&$v!=''){
                $where.=" and ore.createTime<=".(strtotime($v)+24*3600-1);
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
                $where.=" and s.title like '%$v%'";
                continue;
            }
            if($v!=''){
                $where.=" and ore.$k=$v";
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


}