<?php

/**
 * 获取唯一订单号
 */
function get_order_sn($uid=0){
    while (1) { //获取唯一订单号
        $orderSn = get_unique_id($uid);
        $id = M('orders')->where(array('orderSn'=>$orderSn))->getField('id');
        if(!$id){
            break;
        }
    }
    return $orderSn;
}
/**
 * [get_pay_sn 获取唯一支付单号]
 * @param  integer $uid [description]
 * @return [type]       [description]
 */
function get_pay_sn($uid=0){
	while (1) { //获取唯一订单号
        $paySn = get_unique_sn($uid);
        $id = M('orders')->where(array('paySn'=>$paySn))->getField('id');
        if(!$id){
            break;
        }
    }
    return $paySn;
}

function get_unique_id($uid){
	$usn = str_pad(substr($uid, -3), 3, '0', STR_PAD_LEFT);
    return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).get_usn($uid);
}
function get_unique_sn($uid){
    $year_code = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
    return $year_code[intval(date('Y'))-2017].
    strtoupper(dechex(date('m'))).date('d').
    substr(time(),-5).substr(microtime(),2,5).sprintf('d',rand(0,99)).get_usn($uid);
}

function get_usn($uid){
	if($uid){
		return str_pad(substr($uid, -3), 3, '0', STR_PAD_LEFT);
	}
	return '';
}