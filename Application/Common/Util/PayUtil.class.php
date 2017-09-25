<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/24 0024
 * Time: 下午 4:52
 */

namespace Common\Util;


class PayUtil
{
    /**
     * [getPayUrl 微信APP支付]
     * @param  [type] $attach     [附加数据]
     * @param  [type] $tradeNo    [商户订单号]
     * @param  [type] $money      [订单金额]
     * @param  string $body       [商品或支付单简要描述]
     * @param  string $notify_url [通知地址]
     * @return [type]             [description]
     */
    public function getPayUrl($config,$attach,$tradeNo,$money,$body = '',$notify_url = ''){
        require_once "./Api/WxpayAPI/lib/WxPay.Api.php";
        require_once './Api/WxpayAPI/example/log.php';
        //初始化日志
        $logHandler= new \CLogFileHandler("./ApiLogs/wxpayApp/".date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);
        $money = $money*100;//订单金额 ，单位分
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($body);//商品或支付单简要描述
        $input->SetAttach($attach);//附加数据
        $input->SetOut_trade_no($tradeNo);//商户订单号
        $input->SetTotal_fee($money);//订单金额 ，单位分
        $input->SetNotify_url($notify_url);//通知地址
        $input->SetTrade_type('APP');//app支付
        $logData =['paySn'=>$tradeNo,'money'=>$money,'body'=>$body,'notify_url'=>$notify_url];
        \Log::DEBUG("input data :" . json_encode($logData));
        $result = \WxPayApi::unifiedOrder($input,$config);
        \Log::DEBUG("return data :" . json_encode($result));
        if($result['return_code']=='FAIL'){
            ajax_return_error($result['return_msg']);
        }
        if($result['result_code']=='FAIL'){
            ajax_return_error($result['err_code_des']);
        }
        $result['timeStamp'] = time();
        $result['package'] = 'Sign=WXPay';
        $result = $this->getSign($result,$config['key']);
        return $result;
    }
    // appId，partnerId，prepayId，nonceStr，timeStamp，package
    public function getSign($result,$key){
        $data['appid'] = $result['appid'];
        $data['partnerid'] = $result['mch_id'];
        $data['noncestr'] = $result['nonce_str'];
        $data['prepayid'] = $result['prepay_id'];
        $data['timestamp'] = $result['timeStamp'];
        $data['package'] = $result['package'];
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string = $this->ToUrlParams($data);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $data['sign'] = strtoupper($string);
        return $data;
    }
    /**
     * 格式化参数格式化成url参数
     */
    public function ToUrlParams($data)
    {
        $buff = "";
        foreach ($data as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }
    /**
     * [pay 支付宝APP支付]
     * @param  [type] $config     [相关配置]
     * @param  [type] $sn         [商户网站唯一订单号]
     * @param  [type] $body       [商品的标题]
     * @param  [type] $price      [订单总金额]
     * @param  [type] $notify_url [支付宝服务器主动通知地址]
     * @return [type]             [description]
     */
    public function pay($config,$sn,$body,$price,$notify_url){
        require_once "./Api/alipayapp/aop/AopClient.php";
        //构造业务请求参数的集合(订单信息)
        $content = array();
        $content['subject'] = $body;//商品的标题/交易标题/订单标题/订单关键字等
        $content['out_trade_no'] = $sn;//商户网站唯一订单号
        $content['timeout_express'] = '15m';//该笔订单允许的最晚付款时间
        $content['total_amount'] =$price;//订单总金额(必须定义成浮点型)
        $content['product_code'] = 'QUICK_MSECURITY_PAY';//销售产品码，商家和支付宝签约的产品码，为固定值QUICK_MSECURITY_PAY
        $con = json_encode($content);//$content是biz_content的值,将之转化成字符串
        
        //公共参数
        $param = array();
        $Client = new \AopClient();//实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
        $param['app_id'] = $config['app_id'];//支付宝分配给开发者的应用ID
        $param['method'] = 'alipay.trade.app.pay';//接口名称
        $param['charset'] = 'UTF-8';//请求使用的编码格式
        $param['sign_type'] = 'RSA2';//商户生成签名字符串所使用的签名算法类型
        $param['timestamp'] = time();//发送请求的时间
        $param['version'] = '1.0';//调用的接口版本，固定为：1.0
        $param['notify_url'] =$notify_url ;//支付宝服务器主动通知地址
        $param['biz_content'] = $con;//业务请求参数的集合,长度不限,json格式
        //生成签名
        $paramStr = $Client->getSignContent($param);
        $sign = $Client->alonersaSign($paramStr,$config['private_key'],'RSA2',false);
        $param['sign'] = $sign;
        log_result('./ApiLogs/alipayApp/'.date('Y-m-d').'.log','Param data：'.json_encode($param));
        $str = $Client->getSignContentUrlencode($param);
        return ['str'=>$str];
    }
    //微信回调
    public function wxNotify(){
        require_once "./Api/WxpayAPI/lib/WxPay.Api.php";
        require_once "./Api/WxpayAPI/lib/WxPay.PayNotifyCallBack.php";
        require_once './Api/WxpayAPI/example/log.php';
        //初始化日志
        $logHandler= new \CLogFileHandler("./ApiLogs/wxpayApp/".date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);

        $notify = new \WxPayNotifyCallBack();
        //获取回调数据
        $data = $notify->Handle();
        \Log::DEBUG("Notify data :" . json_encode($data));
        return $data;
    }
    //支付宝回调
    public function alipayNotify($config,$param){
        require_once "./Api/alipayapp/aop/AopClient.php";
        $Client = new \AopClient();
        $Client->alipayrsaPublicKey=$config['public_key'];
        $result=$Client->rsaCheckV1($param,null,'RSA2');
        return $result;
    }
    /**
     * 支付宝--退款
     */
    public function aliapyRefund($order){
        require_once "./Api/alipayapp/aop/AopClient.php";
        $file = './ApiLogs/refund/'.date('Y-m-d').'.log';
        log_result( $file,'进入支付宝退款流程');
        log_result( $file,'Param data：'.json_encode($order));
        $config = M('Config')->where(['config'=>'alipay'])->getField('value');
        $config = unserialize($config);
        $aop = new \AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $config['app_id'];
        $aop->rsaPrivateKey = $config['private_key'];
        $aop->alipayrsaPublicKey = $config['public_key'];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='GBK';
        $aop->format='json';
        $request = new \AlipayTradeRefundRequest ();
        $request->setBizContent(json_encode($order));
        $result = $aop->execute ( $request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            log_result( $file,'支付宝退款成功');
//            echo "成功";
            return true;
        } else {
            log_result( $file,'支付宝退款失败 resultCode='.$resultCode);
//            echo "失败";
            return false;
        }
    }
    /**
     * 微信--退款
     */
    public function wxRefund($order){
        require_once "./Api/WxpayAPI/lib/WxPay.Api.php";
        $config = M('Config')->where(['config'=>'wxpay'])->getField('value');
        $config = unserialize($config);
        $file = './ApiLogs/refund/'.date('Y-m-d').'.log';
        log_result( $file,'进入微信退款流程');
        log_result( $file,'Param data：'.json_encode($order));
        $input = new \WxPayRefund();
        $input->SetOut_trade_no($order['out_trade_no']);
        $input->SetTotal_fee($order['payMoney']*100);
        $input->SetRefund_fee($order['refund_amount']*100);
        $input->SetOut_refund_no($order['out_request_no']);
        $input->SetTransaction_id($order['trade_no']);
//        $input->SetOp_user_id('1488645622');
        $result = \WxPayApi::refund($input,$config);

        if( $result['result_code'] == 'SUCCESS' &&  $result['return_code'] == 'SUCCESS' ) {
            log_result( $file,'微信退款成功');
            return true;
        }else{
            log_result( $file,'微信退款失败 result:'.json_encode($result));
            return false;
        }
    }
}