<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/18 0018
 * Time: 下午 3:08
 */

namespace Common\Util;


class ShipUtil
{
    public $EBusinessID;
    public $AppKey;
    public $ReqURL;//即时查询api
    public $ReqURL1;//电子面单地址
    public function __construct()
    {
        $kdn=unserialize(M("config")->where("id=14")->getField("value"));
        $this->AppKey=$kdn['app_key'];
        $this->EBusinessID=$kdn['biz_id'];
        $this->ReqURL="http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx";
        $this->ReqURL1="http://testapi.kdniao.cc:8081/api/EOrderService";
//        $this->ReqURL1="http://api.kdniao.cc/api/Eorderservice";
    }
    public function index($requestData){
        $logisticResult=$this->getOrderTracesByJson($requestData);
        return $logisticResult;
    }
    /**
     * Json方式 查询订单物流轨迹
     */
    public function getOrderTracesByJson($requestData){

        $datas = array(
            'EBusinessID' => $this->EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $this->AppKey);
        $result=$this->sendPost($this->ReqURL, $datas);

        //根据公司业务处理返回的信息......

        return $result;
    }
    public function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }
    public function encrypt($data,$appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }
    /**
     * 获取电子面单
     */
    public function getSheet($eorder){
        //构造电子面单提交信息
        //调用电子面单
        $jsonParam = json_encode($eorder, JSON_UNESCAPED_UNICODE);

        //$jsonParam = JSON($eorder);//兼容php5.2（含）以下

//        echo "电子面单接口提交内容：<br/>".$jsonParam;
        $jsonResult = $this->submitEOrder($jsonParam);
//        echo "<br/><br/>电子面单提交结果:<br/>".$jsonResult;

//解析电子面单返回结果
        $result = json_decode($jsonResult, true);
        return $result;
//        echo "<br/><br/>返回码:".$result["ResultCode"];
//        if($result["ResultCode"] == "100") {
//            echo "<br/>是否成功:".$result["Success"];
//        }
//        else {
//            echo "<br/>电子面单下单失败";
//        }
    }
    /**
     * Json方式 调用电子面单接口
     */
    function submitEOrder($requestData){
        $datas = array(
            'EBusinessID' => $this->EBusinessID,
            'RequestType' => '1007',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $this->AppKey);
        $result=$this->sendPost($this->ReqURL1, $datas);

        //根据公司业务处理返回的信息......

        return $result;
    }
    function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }

            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }
    function JSON($array) {
        $this->arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }

}