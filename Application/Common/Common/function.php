<?php
use Think\Log;

    /**
     * [p 格式化输出]
     * @param  [array] $arr [待处理的数组]
     * @return [type]       [无]
     */
    function p($arr) {
        dump($arr,true, null, false);
    }

    /**
     * [check_mobile 校验 手机格式]
     * @param  [type] $phone [description]
     * @return [type]        [description]
     */
    function check_mobile($phone){
        return preg_match("/1\d{10}$/",$phone);
    }

    /**
     * [check_email 校验邮箱格式]
     * @param  [type] $email [description]
     * @return [type]        [description]
     */
    function check_email($email){
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        return preg_match($pattern,$email);
    }

	  /**
     * 字符串截取，支持中文和其他编码
     * @static
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @param string $suffix 截断显示字符
     * @return string
     */
    function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
        if(function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str,$start,$length,$charset);
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice.'...' : $slice;
    }


    /**
     * CURL快捷方法，post提交数据
     * @param string $url 提交目的地址
     * @param array $data post数据
     * @return url访问结果
     */
    function curl_post($url, $data) {
    	$ch = curl_init ();
    	$header = array ("Accept-Charset: utf-8",'Expect:' );
    	curl_setopt ( $ch, CURLOPT_URL, $url );
    	curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "GET" );
    	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    	curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
    	curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
    	curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
    	curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
    	curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
    	curl_setopt ( $ch, CURLOPT_TIMEOUT, 60 );
    	// 最好加上http_build_query 转换，防止有些服务器不兼容CURLOPT_POSTFIELDS
    	curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query ( $data ) );
    	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    	$result = curl_exec ( $ch );
    	curl_close ( $ch );
    	return $result;
    }

    /**
     * CURL快捷方法，post提交数据
     * @param string $url 提交目的地址
     * @param array $data post数据
     * @return url访问结果
     */
    function curl_get($url) {
        $ch = curl_init ($url);
		curl_setopt($ch, CURLOPT_HEADER , 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec ( $ch );
    	curl_close ( $ch );
    	return $result;    	
    }
    
    /**
     * 发送短信，当前代码以吉信通为短信平台，使用其他平台时需要重写方法
     * @param string $phone 短信目的手机号，格式由短信平台决定，一般为单手机号或者逗号分割的多手机号
     * @param string $content 短信内容
     * @return boolean 是否发送成功
     */
    function getcode($phone,$type){
		include "./Api/MessageSend.class.php";
		$time = 15;
		$code = rand(1000,9999);
		$context = "【唯公商城】您的验证码为：".$code."，此验证码".$time."分钟之内输入有效。";
        $username = "weigong";                            //改为实际账户名
        $password = "M2d59ef9fe";                         //改为实际短信发送密码
        $config = M('Config')->where(['config'=>'sms'])->getField('value');
        $config = unserialize($config);
        if($config['account'] && $config['password']){
            $username = $config['account'];
            $password = $config['password'];
        }else{
            ajax_return_error('短信配置有误');
        }
		
		$mobiles=$phone;					//目标手机号码，多个用半角“,”分隔
		$content=$context;
		$extnumber="";

		//定时短信发送时间,格式 2016-12-06T08:09:10+08:00，null或空串表示为非定时短信(即时发送)
		$plansendtime='';
		//$plansendtime='2016-12-06T08:09:10+08:00'
		$result=\WsMessageSend::send($username, $password, $mobiles, $content,$extnumber,$plansendtime);

		if(!$result->SuccessCounts)
		{
			ajax_return_error('验证码发送失败');
		}
		else
		{
			//ajax_return_ok($result);
			$data['phone'] = $phone;
			$data['context'] = $context;
			$data['endtime'] = time() + $time*60;
			$data['addtime'] = time();
			$data['code'] = $code;
			$data['type'] = $type;
			M('sms_record')->add($data);
			ajax_return_ok([],"验证码发送成功");
		}
	}

    /**
     * 发送短信，当前代码以253云通讯 为短信平台，使用其他平台时需要重写方法
     * @param string $phone 短信目的手机号，格式由短信平台决定，一般为单手机号或者逗号分割的多手机号
     * @param string $content 短信内容
     * @return boolean 是否发送成功
     */
    function getcode2($phone,$type){
        include "./Api/ChuanglanSmsHelper/ChuanglanSmsApi.php";
        $time = 15;
        $code = rand(1000,9999);
        $context = "【唯公商城】您的验证码为：".$code."，".$time."分钟内有效，若非本人操作请忽略。";
        $username = "N0370112";                            //改为实际账户名
        $password = "ZeHn5fyaCM1c47";                         //改为实际短信发送密码
        $config = M('Config')->where(['config'=>'sms'])->getField('value');
        $config = unserialize($config);
        if($config['account'] && $config['password']){
            $username = $config['account'];
            $password = $config['password'];
        }else{
            ajax_return_error('短信配置有误');
        }
        
        $mobiles = $phone;                    //目标手机号码，多个用半角“,”分隔
        $content = $context;
        $clapi  = new ChuanglanSmsApi();
        $result = $clapi->sendSMS($phone,$context,$username,$password);
        $smsInfo = '[目的手机]：' . $phone . '[短信内容]：' . $content;
        if(!is_null(json_decode($result))){
            $output=json_decode($result,true);
            if(isset($output['code'])  && $output['code']=='0'){
                $data['phone'] = $phone;
                $data['context'] = $context;
                $data['endtime'] = time() + $time*60;
                $data['addtime'] = time();
                $data['code'] = $code;
                $data['type'] = $type;
                M('sms_record')->add($data);
                Log::record ( '[短信发送成功]。' . $smsInfo, Log::INFO );
                ajax_return_ok([],"验证码发送成功");
                // echo '短信发送成功！' ;
            }else{
                // echo $output['errorMsg'];
                Log::record ( '[短信发送失败] errorMsg:' . $output['errorMsg'] . '。' . $smsInfo, Log::WARN );
                ajax_return_error('验证码发送失败');
            }
        }else{
                Log::record ( '[短信发送失败] result:' . $result . '。' . $smsInfo, Log::WARN );
                ajax_return_error('验证码发送失败');
        }
        
    }

	function verifyCode($phone,$type,$code){
		$data=M("sms_record")->where(array("phone"=>$phone,"isUse"=>0,"type"=>$type))->order("id desc")->find();
		if($data){
			if(time()<=$data['endtime']){
				if($code==$data['code']){
					
				}else{
					ajax_return_error("验证码输入不正确");
				}
			}else{
				ajax_return_error("请重新获取验证码");
			}
		}else{
			ajax_return_error("请先去获取验证码");
		}
	}
    
    /**
     * 返回格式化信息
     * @param string $msg 信息内容
     * @param string $code 错误码
     * @param number $status 状态 0 错误 ，1 成功
     * @return array
     */
    function msg_return($status = 0  ,$msg = null , $code = 0) {
    	
    	return array ('status' => $status, "code" => $code ,"msg" => $msg );
    }
     
    

    /**
     * ajax 请求正确返回
     * @param string $msg
     * @param array $data
     * @return json
     */
    function ajax_return_ok($data = array(),$msg = ''){
    
    	$result['status'] = 1;
    	$result['data'] = $data;
    	$result['msg'] = $msg ;
    	// 返回JSON数据格式到客户端 包含状态信息
    	header('Content-Type:application/json; charset=utf-8');
    	exit(json_encode($result));
    }
    
    /**
     * ajax 请求错误返回
     * @param string $msg
     * @param string $code
     * @return json
     */
    function ajax_return_error($msg = null,$code = 1){
    	
    	if ($msg == null){
    		$msgDefault = C ( 'E_MSG_DEFAULT' );
    		$result['msg'] = $msgDefault [$code];
    	}else{
    		$result['msg'] = $msg ;
    	}
    	
    	$result['status'] = 0;
    	$result['code'] = $code;
    	// 返回JSON数据格式到客户端 包含状态信息
    	header('Content-Type:application/json; charset=utf-8');
    	exit(json_encode($result));
    }
    
    
    
    /**
     * 返回json
     * @param array $data
     */
    function json_return( $data = array() ){
    	// 返回JSON数据格式到客户端 包含状态信息
    	header('Content-Type:application/json; charset=utf-8');
    	exit(json_encode($data));
    }
    
    /**
     * 手机验证码检测
     * @param string $phone
     * @param string $inputCode
     * @param string $pre :register , login, findpwd
     * @return boolean
     */
    function check_sms_verify_code($phone, $inputCode,$pre = '') {
    	$sendedCode = session ( $pre . '_' . $phone );
    	return $sendedCode && $sendedCode == $inputCode;
    }
    
    /**
     * 清空服务器保存的验证码
     * @param string $phone
     * @return void
     */
    function clear_sms_verify_code($phone ,$pre = '') {
    	session ( $pre . '_' . $phone, null );
    }
    
    /**
     * 用户密码加密方法，可以考虑盐值包含时间（例如注册时间），
     * @param string $pass 原始密码
     * @return string 多重加密后的32位小写MD5码
     */
    function encrypt_pass($pass) {
    	if ('' == $pass) {
    		return '';
    	}
    
    	$salt = C ( 'PASS_SALT', null, '' );
    	return md5 ( sha1 ( $pass ) . $salt );
    }
		/**
     * 格式化参数格式化成url参数
     */
    function to_url_params($data)
    {
        $buff = "";
        foreach ($data as $k => $v)
        {
            if(!is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        
        $buff = trim($buff, "&");
        return $buff;
    }
	function sendAndroidListcast($device_tokens,$msg) {
		require_once('./Api/Umeng/notification/android/AndroidListcast.php');

		$appkey = '59154f81b27b0a6a9500006e';
		$timestamp = time();
		$appMasterSecret = '87mvqup1ur9ifbp2v8id8u7v2bmthhgr';

		try {
			$listcast = new \AndroidListcast();
			$listcast->setAppMasterSecret($appMasterSecret);
			$listcast->setPredefinedKeyValue("appkey", $appkey);
			$listcast->setPredefinedKeyValue("timestamp", $timestamp);
			// Set your device tokens here
			$listcast->setPredefinedKeyValue("device_tokens", $device_tokens);
			$listcast->setPredefinedKeyValue("ticker",$msg['ticker']);
			$listcast->setPredefinedKeyValue("title", $msg['title']);
			$listcast->setPredefinedKeyValue("text", $msg['text']);
			$listcast->setPredefinedKeyValue("after_open","");
			//$listcast->setPredefinedKeyValue("activity", "com.palmble.weldor.activity.MessageNotificationActivity");
			$listcast->setPredefinedKeyValue("production_mode", "true");
			$listcast->setExtraField("ktype", $msg['ktype']);
			// Set extra fields
			//$listcast->setExtraField("test", $msg['text']);
			//print("Sending unicast notification, please wait...\r\n");

			$res = $listcast->send();
			return $res;
		} catch (Exception $e) {
            log_result('./ApiLogs/Umeng/'.date('Y-m-d').'log',$e->getMessage());   
	//		print("Caught exception: " . $e->getMessage());
			//return false;
		}
	}
	function sendIOSListcast($device_tokens ,$msg) {
		require_once('./Api/Umeng/notification/ios/IOSListcast.php');

		$appkey = '59116206734be4681200165f';
		$timestamp = time();
		$appMasterSecret ='d15lmnjmdlwugdopoidvhfqy6rncyyhu';

		try {
			$listcast = new \IOSListcast();
			$listcast->setAppMasterSecret($appMasterSecret);
			$listcast->setPredefinedKeyValue("appkey", $appkey);
			$listcast->setPredefinedKeyValue("timestamp", $timestamp);
			// Set your device tokens here
			$listcast->setPredefinedKeyValue("device_tokens", $device_tokens);
			$listcast->setPredefinedKeyValue("alert", $msg['title']);
			$listcast->setPredefinedKeyValue("badge", 0);
			$listcast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$listcast->setPredefinedKeyValue("production_mode", "true");
			// Set customized fields
	//		$listcast->setExtraField("ktext", $msg['text']);
	//		$listcast->setExtraField("ktype", $msg['ktype']);
			$listcast->setCustomizedField("ktype", $msg['ktype']);
			$res = $listcast->send();
			return $res;
		} catch (Exception $e) {
	//		print("Caught exception: " . $e->getMessage());
            log_result('./ApiLogs/Umeng/'.date('Y-m-d').'log',$e->getMessage());   
			//return false;
		}
	}

/**
 * [sendAnd 友盟推送-安卓]
 * @param  [type] $device_tokens [设备号]
 * @param  [type] $msg           [推送信息：ticker/title/text ]
 * @param  string $extra         [附加信息：type/linkId]
 * @param  string $type          [推送方式：listcast/unicast/broadcast]
 * @return [type]                [description]
 */
function sendAnd($device_tokens,$msg,$extra = '',$type = 'listcast'){
    require_once('./Api/Umeng/Umeng.class.php');
    $umeng = new \Umeng($device_tokens,$msg, $extra);
    switch ($type) {
        case 'unicast':
            return $umeng->sendAndroidUnicast();
            break;
        case 'broadcast':
            return $umeng->sendAndroidBroadcast();
            break;
        default:
            $tokens = explode(',', $device_tokens);
            if(sizeof($tokens)>500){
                return $umeng->sendAndroidFilecast();
            }else{
                return $umeng->sendAndroidListcast();
            }
            break;
    }
    return false;
}

/**
 * [sendIOS 友盟推送-苹果]
 * @param  [type] $device_tokens [设备号]
 * @param  [type] $msg           [推送信息：ticker/title/text ]
 * @param  string $extra         [附加信息：type/linkId]
 * @param  string $type          [推送方式：listcast/unicast/broadcast]
 * @return [type]                [description]
 */
function sendIOS($device_tokens,$msg,$extra = '',$type = 'listcast'){
    require_once('./Api/Umeng/Umeng.class.php');
    $umeng = new \Umeng($device_tokens,$msg, $extra);
    switch ($type) {
        case 'unicast':
            return $umeng->sendIOSUnicast();
            break;
        case 'broadcast':
            return $umeng->sendIOSBroadcast();
            break;
        default:
            $tokens = explode(',', $device_tokens);
            if(sizeof($tokens)>500){
                return $umeng->sendIOSFilecast();
            }else{
                return $umeng->sendIOSListcast();
            }
            break;
    }
    return false;
}

    /**
     * 获取商品分类
     */
    function getCates($fid=0,$is_ajax = 0){
        $lists = M('goodsCate')->where(['status'=>1,'fid'=>$fid])->order('sorts asc,id asc')->field('id,name')->select();
        if($is_ajax){
            return json_encode($lists);
        }else{
            return $lists;
        }
    }

    /**
 * 多个数组的笛卡尔积
*
* @param unknown_type $data
*/
function combineDika() {
    $data = func_get_args();
    $data = current($data);
    $cnt = count($data);
    $result = array();
    $arr1 = array_shift($data);
    foreach($arr1 as $key=>$item) 
    {
        $result[] = array($item);
    }       

    foreach($data as $key=>$item) 
    {                                
        $result = combineArray($result,$item);
    }
    return $result;
}


/**
 * 两个数组的笛卡尔积
 * @param unknown_type $arr1
 * @param unknown_type $arr2
*/
function combineArray($arr1,$arr2) {         
    $result = array();
    foreach ($arr1 as $item1) 
    {
        foreach ($arr2 as $item2) 
        {
            $temp = $item1;
            $temp[] = $item2;
            $result[] = $temp;
        }
    }
    return $result;
}

/**
 * 刷新商品库存, 如果商品有设置规格库存, 则商品总库存 等于 所有规格库存相加
 * @param type $goods_id  商品id
 */
function refresh_stock($goods_id){
    $store_count = M("GoodsSpec")->where(['goodsId'=>$goods_id])->sum('store');
    if($store_count == 0) return true; // 没有使用规格方式 没必要更改总库存

    $res = M("Goods")->where(['id'=>$goods_id])->save(array('stock'=>$store_count)); // 更新商品的总库存
    if($res === false){
        return false;
    }else{
        return true;
    }
}
/**
     * [getSpec 将规格值字符串转化为数组]
     * @param  [type] $key_name [description]
     * @return [type]           [description]
     */
    function getSpec($key_name){
        if(trim($key_name)!=''){
            $name = explode(' ', $key_name);
            $key_name = [];
            foreach ($name as $key => $value) {
                $temp = explode(':', $value);
                $tem['name'] = $temp[0];
                $tem['value'] = $temp[1];
                $key_name[] = $tem;
            }
            return $key_name;
        }else{
            return [];
        }
    }

/**
 * 打印log
 * @param string $file 保存的文件名
 * @param string $word 保存的内容
 */
function log_result($file,$word){
    //检查目录是否存在
    $filename = dirname($file).DIRECTORY_SEPARATOR;
    if (!file_exists($filename)) {
        mkdir($filename, 0777);
    }
    $fp = fopen($file,"a");
    flock($fp, LOCK_EX) ;
    fwrite($fp,"执行日期：".date("Y-m-d H:i:s",time())."\n".$word."\n\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}
function object_array($array){
    if(is_object($array)){
        $array = (array)$array;
    }
    if(is_array($array)){
        foreach($array as $key=>$value){
            $array[$key] = $this->object_array($value);
        }
    }
    return $array;
}
//有兴趣的朋友可以研究一下
function strtr_array(&$str,&$replace_arr) {
    $maxlen = 0;$minlen = 1024*128;
    if (empty($replace_arr)) return $str;
    foreach($replace_arr as $k => $v) {
        $len = strlen($k);
        if ($len < 1) continue;
        if ($len > $maxlen) $maxlen = $len;
        if ($len < $minlen) $minlen = $len;
    }
    $len = strlen($str);
    $pos = 0;$result = '';
    while ($pos < $len) {
        if ($pos + $maxlen > $len) $maxlen = $len - $pos;
        $found = false;$key = '';
        for($i = 0;$i<$maxlen;++$i) $key .= $str[$i+$pos]; //原文：memcpy(key,str+$pos,$maxlen)
        for($i = $maxlen;$i >= $minlen;--$i) {
            $key1 = substr($key, 0, $i); //原文：key[$i] = '\0'
            if (isset($replace_arr[$key1])) {
                $result .= $replace_arr[$key1];
                $pos += $i;
                $found = true;
                break;
            }
        }
        if(!$found) $result .= $str[$pos++];
    }
    return $result;
}

?>