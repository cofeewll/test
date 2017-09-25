<?php
/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login(){
	$admin = session('admin_auth');
	if (empty($admin)) {
		return 0;
	} else {
		return session('admin_auth_sign') == data_auth_sign($admin) ? $admin['uid'] : 0;
	}
}


/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
	//数据类型检测
	if(!is_array($data)){
		$data = (array)$data;
	}
	ksort($data); //排序
	$code = http_build_query($data); //url编码并生成query字符串
	$sign = sha1($code); //生成签名
	return $sign;
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

function array_get_column($arr, $k){
    if(version_compare(PHP_VERSION,'5.5.0','>=')){
        return array_column($arr, $k);
    }

    $returnArr = array();
    foreach($arr as $val){
        if(is_array($val) && isset($val[$k])){
            $returnArr[] = $val[$k];
        }
    }

    return $returnArr;
}
function excelDown($obj, $fileName){
	ob_end_clean();//清除缓冲区,避免乱码
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=\"$fileName\"");
	header('Cache-Control: max-age=0');

	$objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
	$objWriter->save('php://output'); //文件通过浏览器下载
	exit;
}

/**
     * [addNotice 推送消息]
     * @return [type]          [description]
     */
    function addNotice($title,$content,$type,$send_id){
    	if(!$send_id){
    		return false;
    	}
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
        // $msg['ktype'] = 2;
        if( !empty($andChanelId) ) {
            // sendAndroidListcast($andChanelId,$msg);//安卓推送
            sendAnd($andChanelId,$msg,'','unicast');
        }
        return $res;
    }