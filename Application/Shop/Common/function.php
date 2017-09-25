<?php
/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login(){
	$shop = session('shop_auth');
	if (empty($shop)) {
		return 0;
	} else {
		return $shop['shopId'] ;
	}
}

