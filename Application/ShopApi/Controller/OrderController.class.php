<?php
namespace ShopApi\Controller;
use Common\Util\PayUtil;
/**
* 
*/
class OrderController extends CenterController
{
	
	/**
	 * [myOrder 订单列表]
	 * @return [type] [description]
	 */
	public function myOrder(){
        $sid = $this->sid;
        $type = I("type");
        $page = I("page")?:1;
        $num = I("num")?:10;
        $left = ($page-1)*$num;
        $where = "shopId = $sid";
        switch ($type){
            case 1://全部
                break;
            case 2://待发货
                $where.=" and o.status=1";
                break;
            case 3://已发货
                $where.=" and o.status=2";
                break;
            case 4://已完成
                $where.=" and o.status=5";
                break;
        }
        $data = D("Orders")->myOrder($where,$left,$num);
        ajax_return_ok($data,"请求成功");
    }

    /**
     * 售后列表
     */
    public function refundList(){
        $sid = $this->sid;
        $page = I("page")?:1;
        $num = I("num")?:10;
        $left = ($page-1)*$num;
        $data = M("order_refund")->alias("re")
        	->join("wg_orders as o on o.id=re.orderId","left")
            ->join("wg_goods as g on g.id=re.goodsId","left")
            ->join("wg_goods_spec as gs on gs.id=re.gsId","left")
            ->field("re.*,o.orderSn,o.createTime as addTime,name,g.img,g.price,gs.price as sprice")
            ->where("re.shopId=$sid")->order("id desc")->limit($left,$num)->select();
        foreach($data as $k=>$v){
            $data[$k]['img']="http://".$_SERVER['HTTP_HOST'].$v['img'];
            if($v['spec_key_name']){
            	$data[$k]['spec'] = getSpec($v['spec_key_name']);
            	$data[$k]['price'] = $v['sprice'];
            	unset($data[$k]['sprice']);
            }else{
            	unset($data[$k]['sprice']);
            }
            if($v['evidence']){
            	$data[$k]['evidence'] = explode('|', $v['evidence']);
            }else{
            	$data[$k]['evidence'] = [];
            }
            unset($data[$k]['dealTime'],$data[$k]['dealContext'],$data[$k]['isSend'],$data[$k]['gsId'],$data[$k]['gsId'],$data[$k]['type']);
        }
        ajax_return_ok($data,"请求成功");
    }

    /**
     * [getShip description]
     * @return [type] [description]
     */
    public function getShip(){
    	$info = C('ship');
    	$data = [];
    	foreach ($info as $key => $value) {
    		$tem['shipId'] = $key;
    		$tem['shipName'] = $value;
    		$data[] = $tem;
    	}
    	ajax_return_ok($data);
    }

    /**
     * [delivery 发货]
     * @return [type] [description]
     */
    public function delivery(){
    	if(IS_POST){
    		$orderId = I('orderId',0,'intval');
    		$shipId = I('shipId','','trim');
    		$shipSn = I('shipSn','','trim');
    		if( !$orderId || !$shipId || !$shipSn){
    			ajax_return_error('',9);
    		}
    		$ship = C('ship');
    		$sinfo = array_keys($ship);
    		if(!in_array($shipId, $sinfo)){
    			ajax_return_error('物流信息不匹配');
    		}
    		$info = M('orders')->where(['id'=>$orderId,'status'=>1])->find();
    		if(empty($info)){
    			ajax_return_error('订单信息失效或已发货');
    		}
    		$res = M('orders')->where(['id'=>$orderId])->save(['status'=>2,'shipId'=>$shipSn,'shipName'=>$shipId,'shipTime'=>time()]);
    		if($res === false){
    			ajax_return_error('操作失败，请重试');
    		}else{
    			ajax_return_ok([],'操作成功');
    		}

    	}else{
    		ajax_return_error('请求错误');
    	}
    }

    /**
     * [agree 同意售后]
     * @return [type] [description]
     */
    public function agree(){
    	$id = I('id',0,'intval');
    	$realMoney = I('realMoney',0,'floatval');
    	$addMoney = I('addMoney',0,'floatval');
    	if(!$id || !$realMoney){
    		 ajax_return_error("",9);
    	}
    	$order = M("order_refund")->where(['id'=>$id,'status'=>0])->field("orderId,shopId,refundSn,goodsId,amount")->find();
        if(!$order['orderId']){
            ajax_return_error("信息不存在或已处理");
        }
        if($realMoney >$order['amount']){
            ajax_return_error('退款金额不正确');
        }
        $order_info = M("orders")->where(['id'=>$order['orderId']])->field("uid,payType,userMoney,payMoney,orderSn,paySn")->find();
        $model = M();
        $model->startTrans();
        $res1=M("order_refund")->where(['id'=>$id])->save(['realMoney'=>$realMoney,'addMoney'=>$addMoney,'status'=>1,'dealTime'=>time()]);
        //同意退款
        $uid=$order_info['uid'];
        if($addMoney>0){
            //补偿金--加入用户钱包明细
            $res2=D("UserMoney")->addLog($uid,1,$addMoney,$order['shopId'],"商家转赠");
        }else{
            $res2=1;
        }
        if($realMoney>0){
            //执行退款操作
            //1判断支付方式
            $pay_type = $order_info['payType'];
            if($pay_type>0){
                if($order_info['payMoney']<=$realMoney){
                    $money=$order_info['payMoney'];
                }else{
                    $money=$realMoney;
                }
                $money1=$realMoney-$order_info['payMoney'];
                $payutil=new PayUtil();
            }
            $res5=1;$res4=1;
            switch ($pay_type){
                case 0://余额支付
                    $res4=D("UserMoney")->addLog($uid,4,$realMoney,$order['shopId'],"商品退款");
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
            $res3 = M("order_goods")->where("orderId={$order['orderId']} and goodsId={$order['goodsId']}")
                ->setField("status",3);
            //判断是否部分退货
            $res6 = D("Orders")->partReturn($order['orderId']);
            if($res1&&$res2&&$res3&&$res4&&$res5&&($res6!==false)){
                $model->commit();
                ajax_return_ok([],"处理成功");
            }else{
                $model->rollback();
                ajax_return_error("处理失败");
            }

        }else{
            ajax_return_error("请输入有效的退款金额");
        }
    }

    /**
     * [refuse 拒绝售后]
     * @return [type] [description]
     */
    public function refuse(){
    	$id = I('id',0,'intval');
    	$context = I('context','','trim');
    	if(!$id || !$context){
    		ajax_return_error("",9);
    	}
    	$order = M("order_refund")->where(['id'=>$id,'status'=>0])->field("orderId,shopId,refundSn,goodsId")->find();
        if(!$order['orderId']){
            ajax_return_error("信息不存在或已处理");
        }
        $model = M();
        $model->startTrans();
        $res1 = M("order_refund")->where(['id'=>$id])->save(['status'=>2,'dealTime'=>time(),'dealContext'=>$context]);
    	$res2 = M("orders")->where(['id'=>$order['orderId']])->setField("status",2);
        $res3 = M("order_goods")->where(['orderId'=>$order['orderId'],'goodsId'=>$order['goodsId']])
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