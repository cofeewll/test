<?php
namespace Api\Controller;
use Common\Util\PayUtil;

/**
* 购物车、下单接口
*/
class CartController extends CenterController
{
	/**
	 * [addCart 商品加入购物车]
	 */
	public function addCart(){
		$uid = $this->uid;
		if(IS_POST){
			$goodsId = I('goodsId',0,'intval');
			$number = I('number',1,'intval');
			$key = I('key','','trim');
			//$gsId = I('gsId',0,'intval');
			$gsId = 0;
			if(!$goodsId || !$number){
				ajax_return_error('',9);
			}
			$goodsInfo = M('Goods')->field('id,shopId,price,stock')
						->where(['id'=>$goodsId,'status'=>1])->find();
			if(empty($goodsInfo)){
				ajax_return_error('该商品已下架');
			}
			$spec_count = M('GoodsSpec')->where(['goodsId'=>$goodsId])->count();
			if( $spec_count>0 && $key == ''){
				ajax_return_error('请选择商品规格');
			}
			$spec = [];
			if($key){
				$spec = $this->getGoodsSpec($goodsId,$key);
				$gsId = $spec['gsId'];
			}

			$data = array(
                'shopId' => $goodsInfo['shopId'],
                'goodsId' => $goodsId,
                'gsId' => $gsId,
                'uid' => $uid,
                'price' => $goodsInfo['price'],
                'number' => $number,
                'createTime' => NOW_TIME,
            );
            $cinfo = M('cart')->where(array('gsId'=>$gsId,'goodsId'=>$goodsId,'uid'=>$uid))->find();
            if($cinfo){
                $data['number'] = $data['number'] + $cinfo['number'];
            }
			if($gsId){
				// $spec = M('GoodsSpec')->field('key,key_name,price,store')->where(['goodsId'=>$goodsId,'key'=>$key])->find();
				if(empty($spec)){
					ajax_return_error('所选规格不存在');
				}
				if($spec['store'] < $data['number']){
                    ajax_return_error('库存不足');
                }
                $data['price'] = $spec['price'];
                $data['spec_key'] = $spec['key'];
                $data['spec_key_name'] = $spec['key_name'];
			}else{
				if($goodsInfo['stock'] < $data['number']){
                    ajax_return_error('库存不足');
                }
			}
			if($cinfo){
                $res = M('cart')->where(array('id'=>$cinfo['id']))->save($data);
            }else{
                $res = M('cart')->add($data);
            }
            if($res){
                ajax_return_ok([],'成功加入购物车');
            }else{
                ajax_return_error('加入购物车失败');
            }
		}else{
			ajax_return_error('请求错误');
		}
	}

	/**
	 * [getCart 获取购物车列表]
	 * @return [type] [description]
	 */
	public function getCart(){
		$uid = $this->uid;
		$goodsList = D('CartView')
					->where(['uid'=>$uid])
					->order('createTime desc')
					->select();
		$shopIds = array_unique(array_column($goodsList,'shopId'));
		$lists = [];
		$ind = 0;
		foreach ($shopIds as $k => $sid) {
			$goods = [];
			foreach ($goodsList as $key => $info) {
				if($sid == $info['shopId']){
					$storeNum = $info['store'] ? $info['store'] : $info['stock'];
					if($info['status'] != 1 || $info['pstatus']!=1 || $storeNum == 0){
						if( M('cart')->where(['id' => $info['id']])->delete() ){
							continue;
						}else{
							ajax_return_error('系统开小差了，请重试');
						}
					}else{
						$num = $info['number']<=$storeNum ? $info['number'] : $storeNum;
						$goods[] = [
							'id' => $info['id'],
							'goodsId' => $info['goodsId'],
							'name' => $info['name'],
							'img' => C('SITE_ROOT').$info['img'],
							'gsId' => $info['gsId'],
							'spec_key_name' => $info['spec_key_name'],
							'spec' => getSpec($info['spec_key_name']),
							'price' => $info['price'],
							'number' => $num,
							'store' => $storeNum,
						];
						$lists[$ind]['shopId'] = $info['shopId'];
						$lists[$ind]['title'] = $info['title'];
						$lists[$ind]['qq'] = $info['qq'];
					}
				}
			}
			$lists[$ind]['glist'] = $goods;
			$ind ++;
		}
		// trace($lists);
		ajax_return_ok($lists);
	}

	/**
	 * [changeNum 修改购物车中商品数量]
	 * @return [type] [description]
	 */
	public function changeNum(){
		if(IS_POST){
			$id = I('id',0,'intval');
			$number = I('number',0,'intval');
            if(!$id||!$number){
                ajax_return_error('',9);
            }
            $storeNums = 0;
            $cinfo = D('CartView')->where(['id'=>$id])->find();
            trace($cinfo);
            if(!$cinfo){
                ajax_return_error('商品信息不存在');
            }
            if($cinfo['gsId']){
                if($number > intval($cinfo['store'])){
                    $number = intval($cinfo['store']);
                }
                $storeNums = intval($cinfo['store']);
            }else{
                if($number > intval($cinfo['stock'])){
                    $number = intval($cinfo['stock']);
                }
                $storeNums = intval($cinfo['stock']);
            }
            $res = M('Cart')->where(array('id'=>$id))->save(array('number'=>$number));
            if($res === false){
            	ajax_return_error('操作失败');
            }else{
            	$data['number'] = $number;
	            $data['store'] = $storeNums;
	            ajax_return_ok($data);
            }
        }else{
            ajax_return_error('请求错误');
        }
	}

	/**
	 * [cartDel 删除购物车商品]
	 * @return [type] [description]
	 */
    public function cartDel(){
        if(IS_POST){
        	$ids = I('ids','','trim');
            if($ids == ''){
	            ajax_return_error('',9);
	        }
	        $ids = str_replace('|', ',', $ids);
	        $map['uid'] = $this->uid;
	        $map['id'] = array('in',$ids);
	        $res = M('Cart')->where($map)->delete();
	        if($res){
	            ajax_return_ok([],'操作成功');
	        }else{
	            ajax_return_error('操作失败');
	        }
        }else{
        	ajax_return_error('请求错误');
        }
    }

    /**
     * [getAddress 获取默认地址]
     * @return [type] [description]
     */
    public function getMyAddress(){
    	$data = $this->getAddress(0);
    	ajax_return_ok($data);
    }

    /**
     * [getShipInfo 确认订单 type=1直接购买 type=2 购物车下单]
     * @return [type] [description]
     */
    public function getShipInfo(){
    	if(IS_POST){
    		$type = I('type',0,'intval');
    		$aid = I('aid',0,'intval');
    		if($type == 1){
    			$goodsId = I('goodsId',0,'intval');
				$number = I('number',1,'intval');
				// $gsId = I('gsId',0,'intval');
				$key = I('key','','trim');
				if(!$goodsId || !$number){
					ajax_return_error('',9);
				}

				$result = $this->getInfo($goodsId,$key,$number,$aid);
    		}elseif($type ==2){
    			$cartIds = I('cartIds','','trim');
	            if($cartIds == ''){
		            ajax_return_error('',9);
		        }
		        $cartIds = str_replace('|', ',', $cartIds);
	        	$result = $this->getCartInfo($cartIds,$aid);
    		}else{
    			ajax_return_error('',9);
    		}
	        ajax_return_ok($result);
        }else{
        	ajax_return_error('请求错误');
        }
    }

    /**
     * [addOrder 确认下单]
     */
    public function addOrder(){
    	$uid = $this->uid;
    	if(IS_POST){
    		$type = I('type',0,'intval');
    		$aid = I('aid',0,'intval');
    		$param = I('param','','trim');
    		if(!$aid){
    			ajax_return_error('',9);
    		}
    		if($type == 1){
    			$goodsId = I('goodsId',0,'intval');
				$number = I('number',1,'intval');
				// $gsId = I('gsId',0,'intval');
				$key = I('key','','trim');

				if(!$goodsId || !$number){
					ajax_return_error('',9);
				}
				
				//查找该商品相同信息订单是否存在
				// $rec = M('Order')->alias('o')->field('o.id as oid,og.id as ogid,')
				// 		->join('wg_order_goods as og on o.id = og.orderId')
				// 		->where(['o.status'=>0,'og.goodsId'=>$goodsId,'gsId'=>$gsId,'og.number'=>$number]);
				$result = $this->getInfo($goodsId,$key,$number,$aid,1);
    		}elseif($type == 2){
    			$cartIds = I('cartIds','','trim');
	            if($cartIds == ''){
		            ajax_return_error('',9);
		        }
		        $cartIds = str_replace('|', ',', $cartIds);
	        	$result = $this->getCartInfo($cartIds,$aid,1);
	        	$cartIds = $result['cartIds'];
    		}else{
    			ajax_return_error('',9);
    		}
    		M()->startTrans();
    		$info = $this->addOrderInfo($result,$param);
    		if($info == false){
    			M()->rollback();
    			ajax_return_error('提交失败');
    		}
    		if($type == 2 && $cartIds){
    			//删除购物车记录
    			if( !M('Cart')->where(['id'=>['in',$cartIds]])->delete() ){
    				M()->rollback();
    				ajax_return_error('提交失败');
    			}
    		}
    		M()->commit();
    		$userMoney = M('User')->where(['id'=>$uid])->getField('money');
    		$data['totalAmount'] = $result['totalAmount'];
    		$data['orderIds'] = implode('|', $info['orderIds']);
    		$data['orderSns'] = implode('|', $info['orderSns']);
    		$data['userMoney'] = number_format($userMoney,2,'.','');
	        ajax_return_ok($data);
        }else{
        	ajax_return_error('请求错误');
        }
    }

    /**
     * [payOrder 支付订单-返回支付成功/失败]
     * @return [type] [description]
     */
    public function payOrder(){
    	if(IS_POST){
    		$orderIds = I('orderIds','','trim');
	    	$totalMoney = I('totalMoney',0,'floatval');
	    	$payType = I('payType',0,'intval');
	    	$use = I('use',0,'intval');
	    	if($orderIds == '' || !$payType || !$use || !$totalMoney ){
	    		ajax_return_error('',9);
	    	}
	    	$uid = $this->uid;
	    	$orderIds = explode('|', $orderIds);
	    	$orders = M('Orders')->field('id,orderSn,amount,status')
	    			->where(['uid'=>$uid,'id'=>['in',$orderIds]])
	    			->select();
	    	if(empty($orders)){
	    		ajax_return_error('订单信息不存在');
	    	}
	    	$totalAmount = 0;
	    	foreach ($orders as $k => $order) {
	    		if( $order['status'] != 0 ){
	    			ajax_return_error('支付失败，订单已支付或失效');
	    		}
	    		$totalAmount += $order['amount'];
	    	}
	    	if($totalMoney != $totalAmount){
	    		ajax_return_error('支付金额错误');
	    	}

	    	$paySn = get_pay_sn($uid);
	    	$newData = [
	    		'payType' => $payType,
	    		'paySn' => $paySn,
	    	];

	    	
	    	$userMoney = M('User')->where(['id'=>$uid])->getField('money');
	    	if($use == 1 && $userMoney > 0){
	    		if( $userMoney >= $totalMoney ){	//全部使用余额支付
	    			M()->startTrans();
	    			$newData['payType'] = 0;
	    			foreach ($orders as $key => $value) {
	    				$newData['userMoney'] = $value['amount'];
	    				$newData['payMoney'] = 0;
	    				$newData['status'] = 1;
	    				$newData['payTime'] = time();
	    				$res = M('Orders')->where(['id'=>$value['id']])->save($newData);
	    				if($res === false){
	    					M()->rollback();
	    					ajax_return_error('支付失败');
	    				}
	    			}
	    			//支付成功后续操作
	    			if( !D('CartView')->afterPay($paySn)){
	    				M()->rollback();
	    				ajax_return_error('支付失败');
	    			}
	    			M()->commit();
	    			ajax_return_ok([],'支付成功');
	    		}else{
	    			if($payType != 1 && $payType != 2){
	    				ajax_return_error('支付方式有误');
	    			}
	    			$left = $userMoney;
	    			//更新订单信息
	    			M()->startTrans();
	    			foreach ($orders as $key => $value) {
	    				if($left >= $value['amount']){
	    					$newData['payType'] = 0;
	    					$newData['userMoney'] = $value['amount'];
	    					$newData['payMoney'] = 0;
	    					$left -=  $value['amount'];
	    				}else{
	    					$newData['userMoney'] = $left;
	    					$newData['payMoney'] = floatval($value['amount'] - $left);
	    					$left =  0;
	    				}
	    				$res = M('Orders')->where(['id'=>$value['id']])->save($newData);
	    				if($res === false){
	    					M()->rollback();
	    					ajax_return_error('支付失败');
	    				}
	    			}
	    			M()->commit();
	    			//第三方支付总金额
	    			$payMoney = floatval($totalMoney - $userMoney);
	    			$result = $this->pay($payType,$payMoney,$paySn);
	    			ajax_return_ok($result);
	    		}
	    	}else{
	    		$newData['userMoney'] = 0;
	    		//更新订单信息
    			M()->startTrans();
    			foreach ($orders as $key => $value) {
    				$newData['payMoney'] = $value['amount'];
    				$res = M('Orders')->where(['id'=>$value['id']])->save($newData);
    				if($res === false){
    					M()->rollback();
    					ajax_return_error('支付失败');
    				}
    			}
    			M()->commit();
    			//第三方支付总金额
    			$result = $this->pay($payType,$totalMoney,$paySn);
    			ajax_return_ok($result);
	    	}
    	}else{
    		ajax_return_error('请求错误');
    	}
    }

    /**
     * [pay 第三方支付]
     * @return [type] [description]
     */
    protected function pay($payType,$payMoney,$paySn){
		$body = "唯公商城-订单支付";
		$pay = new PayUtil();
		if($payType == 1){	//支付宝支付
			// $config = C("alipay");
			$config = M('Config')->where(['config'=>'alipay'])->getField('value');
        	$config = unserialize($config);
        	if(empty($config['app_id']) || empty($config['public_key']) || empty($config['private_key'])){
        		ajax_return_error('支付配置参数有误');
        	}
            $notify_url = 'http://'.$_SERVER['HTTP_HOST'].'/index.php/Api/Notify/alipay';
            $result = $pay->pay($config,$paySn,$body,$payMoney,$notify_url);
		}elseif($payType == 2){		//微信支付
			$config = M('Config')->where(['config'=>'wxpay'])->getField('value');
        	$config = unserialize($config);
        	if(empty($config['app_id']) || empty($config['mch_id']) || empty($config['key'])){
        		ajax_return_error('支付配置参数有误');
        	}
			$attach = "";
            $notify_url = 'http://'.$_SERVER['HTTP_HOST']. '/index.php/Api/Notify/weixin';
            $result = $pay->getPayUrl($config,$attach,$paySn,$payMoney,$body,$notify_url);
		}
		return $result;
    }


	/**
	 * [addOneOrder 添加订单信息]
	 */
	protected function addOrderInfo($infoArr,$param){
		$uid = $this->uid;
		//获取积分兑换比例
		$rate = M('config')->where(['config'=>'buyScore','flag'=>1])->getField('value');
		$rate = floatval($rate);
		$temp = [];
		if(trim(trim($param,'|'),'#') != ''){
			$param = explode('|', $param);
			foreach ($param as $k => $v) {
				$v = explode('#', $v);
				$temp[$v[0]] = $v[1];
			}
		}
		$orderIds = [];
		$orderSns = [];
		$address = $infoArr['address'];
		$orderData = [
			'uid' => $uid,
			'address' => $address['province'].'|'.$address['city'].'|'.$address['county'].'|'.$address['detail'],
			'realname' => $address['receiver'],
			'phone'	=>$address['phone'],
			'status' => 0,
		];
		foreach ($infoArr['lists'] as $key => $info) {
			$orderSn = get_order_sn($uid);
			$score = $info['goodsAmount'] * $rate;
			$orderData['orderSn'] = $orderSn;
			$orderData['shopId'] = $info['shopId'];
			$orderData['amount'] = $info['total'];
			$orderData['goodsAmount'] = $info['goodsAmount'];
			$orderData['score'] = $score;
			$orderData['fee'] = $info['fee'];
			$orderData['createTime'] = time();
			$orderData['oYear'] = date('Y');
			$orderData['oMonth'] = date('m');
			$orderData['oDate'] = date('d');
			$orderData['remark'] = '无';
			
			if($temp){
				$tem_key =array_keys($temp);
				$key = $info['shopId'].'_remark';
				if(in_array($key, $tem_key)){
					$orderData['remark'] = $temp[$key];
				}
			}
			$orderId = M('Orders')->add($orderData);
			if(!$orderId){
				return false;
				break;
			}
			$orderIds[] = $orderId;
			$orderSns[] = $orderSn;
			$gdata = [];
			if($info['glist']){
				foreach ($info['glist'] as $k => $good) {
					$goodsData = [
						'orderId' =>$orderId,
						'goodsId' => $good['goodsId'],
						'shopId' => $info['shopId'],
						'cid' => $good['cid'],
						'gsId' => $good['gsId'],
						'number' => $good['number'],
						'spec' => $good['spec_key_name'],
						'price' => $good['price'],
						'goodsAmount' => floatval($good['number']*$good['price']),
						'addTime' => strtotime(date('Y-m-01')),
						'status' => 1,
					];
					$gdata[] = $goodsData;
				}
			}
			if($gdata){
				if( !M('OrderGoods')->addAll($gdata) ){
					return false;
					break;
				}
			}
		}
		return ['orderIds'=>$orderIds,'orderSns'=>$orderSns];
	}

	/**
     * [getCartInfo 根据购物车ids,地址信息获取订单基本信息和运费信息]
     * @param  [type]  $ids [购物车ids]
     * @param  integer $aid [收货地址id]
     * @return [type]       [description]
     */
    protected function getCartInfo($ids,$aid = 0,$show = 0){
    	$uid = $this->uid;
    	//获取地址信息
		$address = $this->getAddress($aid);
		
    	$goodsList = D('CartView')
					->where(['uid'=>$uid,'id'=>['in',$ids],'pstatus'=>1])
					->order('createTime desc')
					->select();
		if(empty($goodsList)){
			ajax_return_error('请选择下单商品');
		}
		$shopIds = array_unique(array_column($goodsList,'shopId'));
		$lists = [];
		$totalAmount = 0;
		$ind = 0;
		$cardIds = [];
		foreach ($shopIds as $k => $sid) {
			$shopId = 0;
			$title = '';
			$fullMoney = -1;
			$goods = [];
			$total = 0;
			$fee = 0;
			$goodsNum = 0;
			foreach ($goodsList as $key => $info) {
				if($sid == $info['shopId']){
					$storeNum = $info['store'] ? $info['store'] : $info['stock'];
					if($info['status'] != 1 || $storeNum == 0){
						if( M('cart')->where(['id' => $info['id']])->delete() ){
							continue;
						}else{
							ajax_return_error('系统开小差了，请重试');
						}
					}else{
						$cardIds[] = $info['id'];
						$goodsNum ++;
						$num = $info['number']<=$storeNum ? $info['number'] : $storeNum;
						$goods[] = [
							'id' => $info['id'],
							'goodsId' => $info['goodsId'],
							'cid' => $info['cid'],
							'name' => $info['name'],
							'img' => C('SITE_ROOT').$info['img'],
							'gsId' => $info['gsId'],
							'spec_key_name' => $info['spec_key_name'],
							'spec' => getSpec($info['spec_key_name']),
							'price' => $info['price'],
							'number' => $num,
							'store' => $storeNum,
						];
						if($address && in_array($address['province'], explode('|', $info['provinces']))){
							$ship = M('GoodsShip')->where(['goodsId'=>$info['goodsId'],'province'=>$address['province']])->find();
							if($ship){
								$info['shipFee'] = $ship['fee'];
							}
						}
						$total += floatval($info['price'] * $info['number']);
						$fee += floatval($info['shipFee']);
						$shopId = $info['shopId'];
						$title = $info['title'];
						$fullMoney = $info['fullMoney'];
					}
				}
			}
			if( $total >= $fullMoney){
				$fee = 0;
			}
			if( $goodsNum >0 ){
				$lists[$ind]['shopId'] = $shopId;
				$lists[$ind]['title'] = $title;
				if($show){
					$lists[$ind]['glist'] = $goods;
					$lists[$ind]['goodsAmount'] = $total;
				}
				$lists[$ind]['fee'] = number_format($fee,2,'.','');
				$lists[$ind]['total'] = number_format(floatval($total+$fee),2,'.','');

				$totalAmount += $lists[$ind]['total'];
				$ind ++;
			}
		}
		if($show){
			$data['address'] = $address;
			$data['cartIds'] = $cardIds;
		}
		$data['lists'] = $lists;
		$data['totalAmount'] = number_format($totalAmount,2,'.','');
		
		return $data;
    }

	/**
	 * [getInfo 根据商品信息获取订单基本信息]
	 * @param  [type]  $goodsId [商品id]
	 * @param  [type]  $gsId    [规格id]
	 * @param  [type]  $number  [数量]
	 * @param  integer $aid     [地址id]
	 * @return [type]           [description]
	 */
	protected function getInfo($goodsId,$key,$number,$aid=0,$show = 0){
		$uid = $this->uid;
		$goodsInfo = M('Goods')->field('wg_goods.id as goodsId,wg_goods.img,cid,name,price,shipFee,shopId,title,qq,stock,fullMoney,provinces')
					->join('wg_shop on wg_shop.id = wg_goods.shopId')
					->where(['wg_goods.id'=>$goodsId,'wg_goods.status'=>1,'wg_shop.status'=>1])
					->find();
		if(empty($goodsInfo)){
			ajax_return_error('该商品已下架');
		}
		$spec_count = M('GoodsSpec')->where(['goodsId'=>$goodsId])->count();
		if( $spec_count>0 && $key == ''){
			ajax_return_error('请选择商品规格');
		}
		$goodsInfo['img'] = C('SITE_ROOT').$goodsInfo['img'];
		$gsId = 0;
		if($key){
			$spec = $this->getGoodsSpec($goodsId,$key);
			if(empty($spec)){
				ajax_return_error('所选规格不存在');
			}
			$gsId = $spec['gsId'];
			if($number>$spec['store']){
				ajax_return_error('库存不足');
			}
			$goodsInfo['stock'] = $spec['store'];
			$goodsInfo['price'] = $spec['price'];
			$goodsInfo['spec_key_name'] = $spec['key_name'];
			$goodsInfo['spec'] = getSpec($spec['key_name']);
		}else{
			if($number>$goodsInfo['stock']){
				ajax_return_error('库存不足');
			}
			$goodsInfo['spec'] = [];
		}

		$data = [];
		$fee = $goodsInfo['shipFee'];
		//获取用户默认地址信息
		$address = $this->getAddress($aid);
		if($address && in_array($address['province'], explode('|', $goodsInfo['provinces']))){
			$ship = M('GoodsShip')->where(['goodsId'=>$goodsId,'province'=>$address['province']])->find();
			if($ship){
				$fee = $ship['fee'];
			}
		}
		$goodsPrice = number_format(floatval($goodsInfo['price'] * $number),2,'.','');
		if($goodsPrice >= $goodsInfo['fullMoney']){	//到达包邮金额
			$fee = number_format(0,2,'.','');
		}
		$total = number_format(floatval($goodsPrice + $fee),2,'.','');
		$temp = [
			'shopId'=>$goodsInfo['shopId'],
			'title'=>$goodsInfo['title'],
			'fee'=>$fee,
			'total'=>$total,
		];
		if($show){
			$temp['goodsAmount'] = $goodsPrice;
			$temp['glist'][] = [
				'goodsId'=>$goodsInfo['goodsId'],
				'cid' => $goodsInfo['cid'],
				'name'=>$goodsInfo['name'],
				'img'=>$goodsInfo['img'],
				'gsId'=>$gsId,
				'spec_key_name'=>$goodsInfo['spec_key_name']?$goodsInfo['spec_key_name']:'',
				'spec'=>$goodsInfo['spec'],
				'price'=>$goodsInfo['price'],
				'number'=>$number,
				'store'=>$goodsInfo['stock'],
			];
		}
		if($show){
			$data['address'] = $address;
		}
		$data['lists'][] = $temp;
		$data['totalAmount'] = $total;
		return $data;
	}


	/**
	 * [getGoodsSpec 获取商品规格]
	 * @return [type] [description]
	 */
	protected function getGoodsSpec($id,$key){
		$key = trim(str_replace('|', '_', $key),'_');
		$rec = M('GoodsSpec')->where(['goodsId'=>$id,'key'=>$key])->field('id as gsId,key,key_name,price,store')->find();
		if( empty($rec) || $rec['store']<=0 ){
			ajax_return_error('所选规格库存不足');
		}
		return $rec;
	}


	/**
	 * [getAddress 获取默认地址信息或指定地址信息]
	 * @param  integer $aid [description]
	 * @return [type]       [description]
	 */
	protected function getAddress($aid = 0){
		$uid = $this->uid;
		$map = ['uid'=>$uid];
		if($aid){
			$map['id'] = $aid;
		}else{
			$map['isDefault'] = 1;
		}
		$address = M('address')->field('id,receiver,phone,province,city,county,detail')
				->where($map)->find();
		if($aid && empty($address)){
			ajax_return_error('地址信息不存在');
		}
		return $address?$address:[];
	}
}