<?php
namespace Api\Model;
use Think\Model\ViewModel;
/**
* 购物车模型
*/
class CartViewModel extends ViewModel
{
	public $viewFields = array(
		'cart' => array('id','uid','goodsId','shopId','gsId','spec_key_name','price','number','createTime','_as'=>'c','_type'=>'LEFT'),
		'goods' => array('name','img','stock','shipFee','cid','status','_on'=>'c.goodsId = goods.id','_type'=>'LEFT'),
		'shop' => array('title','fullMoney','qq','status'=>'pstatus','provinces','_on'=>'shop.id = c.shopId','_type'=>'LEFT'),
		'goods_spec' => array('store','_on'=>'goods_spec.id = c.gsId','_type'=>'LEFT'),
	);


	/**
	 * [afterPay 处理支付成功后一系列操作-增加商品、商家销量、减去商品库存、总库存、使用余额则减少用户余额、记录余额变化]
	 * @param  [type] $paySn [支付单号]
	 * @return [type]        [description]
	 */
	public function afterPay($paySn,$file = ''){
		$orders = M('Orders')->field('id,uid,shopId,amount,userMoney,payMoney,status')
                ->where(array('paySn'=>$paySn))
                ->select();
        $userMoney = 0;
        $uid = 0;
        foreach ($orders as $k => $order) {
        	$userMoney += $order['userMoney'];
        	$uid = $order['uid'];
        	if( !$this->minusStock($order['id']) ){
        		if(!empty($file)) log_result($file,'function afterPay：修改订单对应商品库存销量失败！');
        		return false;
        	}
            //向商家发送通知
            D('Orders')->addNotice('订单通知','您有新的待发货订单，请及时处理。',2,$order['shopId']);
        }
        if($userMoney>0 && $uid){
        	if( !$this->changeMoney($uid,$userMoney) ){
        		if(!empty($file)) log_result($file,'function afterPay：修改用户余额失败！');
        		return false;
        	}
        }
        if(!empty($file)) log_result($file,'function afterPay：successful！');
        
        return true;
	}

	/**
	 * 根据 order_goods 表扣除商品库存-增加销量
	 * @param type $order_id  订单id
	 */
	public function minusStock($order_id){
	    $orderGoodsArr = M('OrderGoods')->where(["orderId" => $order_id])->select();
	    $num = 0;
	    foreach($orderGoodsArr as $key => $val)
	    {
	        // 有选择规格的商品
	        if(!empty($val['gsId']))
	        {   // 先到规格表里面扣除数量 再重新刷新一个 这件商品的总数量
	            $res0 = M('GoodsSpec')->where(['goodsId'=>$val['goodsId'],'id'=>$val['gsId']])->setDec('store',$val['number']);
	            if($res0 === false) return false;
	            $res1 = refresh_stock($val['goodsId']);
	            if($res1 == false) return false;
	        }else{
	            $res2 = M('Goods')->where(["id"=>$val['goodsId']])->setDec('stock',$val['number']); // 直接扣除商品总数量
	            if($res2 === false) return false;
	        }
	        $res3 = M('Goods')->where(["id"=>$val['goodsId']])->setInc('sellNum',$val['number']); // 增加商品销售量
	        if($res3 === false) return false;
	        $num += $val['number'];
	    }
	    //增加商家销量
	    $res4 = M('Shop')->where(["id"=>$val['shopId']])->setInc('sellNum',$val['number']); 
	    if($res4 === false) return false;
	    return true;
	}

	/**
     * [changeMoney 使用余额支付，支付成功后，修改用户余额]
     * @param  [type] $uid  [支付用户]
     * @param  [type] $money  [使用金额]
     * @return [type]         [description]
     */
    public function changeMoney($uid,$money){
    	$before = M('User')->where(['id'=>$uid])->getField('money');
    	$res = M('User')->where(['id'=>$uid])->setDec('money',$money);
    	if( $res === false ){
    		return false;
    	}
    	$after = floatval($before - $money);
    	$data = [
    		'uid' => $uid,
    		'type' => 3,
    		'cmoney' => $money,
    		'beforeMoney' => $before,
    		'afterMoney' => $after,
    		'createTime' => time(),
    	];
    	$res = M('userMoney')->add($data);
    	if(!$res){
    		return false;
    	}
    	return true;
    }
}