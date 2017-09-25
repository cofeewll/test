<?php
/**
 * Created by PhpStorm.
 * User: xiaolei
 * Date: 2017/4/26
 * Time: 14:24
 */

namespace Api\Controller;


use Common\Util\PayUtil;
use Think\Controller;

class NotifyController extends Controller
{
    //微信支付回调
    public function weixin(){

        $payUtil = new PayUtil();
        $notify = new \WxPayNotifyCallBack();
        //获取回调数据
        $data = $payUtil->wxNotify();
        if ($data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS'){//成功
            require_once './Api/WxpayAPI/example/log.php';
            $logHandler= new \CLogFileHandler("./ApiLogs/wxpayApp/".date('Y-m-d').'.log');
            $log = \Log::Init($logHandler, 15);
            \Log::DEBUG("result :  ok ");
            // $attach =explode('|',trim($data['attach'],'|'));
            $info['paySn'] = $data['out_trade_no'];
            $info['money'] = $data['total_fee']/100;
            $info['trade_no'] = $data['transaction_id'];
            $info['payType'] = 2;
            if($this->ModifyOrder($info,2)){
                $notify->myhandle(true,false);
            }else{
                $notify->myhandle(false,false);
            }

        }else{
            \Log::DEBUG("result :  error ");
            $notify->myhandle(false,false);

        }

    }

    /**
     * 支付宝回调
     */
    public function alipay(){
        $file = './ApiLogs/alipayApp/'.date('Y-m-d').'.log';
        $param = $_POST;
        log_result($file,'Notify data：'.json_encode($param));
        $config = M('Config')->where(['config'=>'alipay'])->getField('value');
        $config = unserialize($config);
        $payUtil = new PayUtil();
        $result = $payUtil->alipayNotify($config,$param);
        log_result($file,'Check Sign result：'.$result);
        $pay_sn = $param['out_trade_no'];
        if($result){
            $info = array(
                'paySn' => $param['out_trade_no'],
                'trade_no' => $param['trade_no'],
                'payType' => 1,
                'money' => $param['total_amount'],
            );
            if($param['trade_status'] == 'TRADE_SUCCESS'){
                $this->ModifyOrder($info,1);
                echo 'success';
            }else{
                echo 'failure';
            }
        }else{
            echo 'success';
        }
    }

    /**
     * [ModifyOrder 修改订单状态]
     * @param [type] $info [支付信息-paySn/money/payType/trade_no]
     */
    private function ModifyOrder($info,$type){
        if($type == 1){
            $file = './ApiLogs/alipayApp/'.date('Y-m-d').'.log';
        }else{
            $file = './ApiLogs/WxpayApp/'.date('Y-m-d').'.log';
        }
        $rec = M('Orders')->field('sum(amount) as totalAmount,sum(userMoney) as totalUser,sum(payMoney) as totalPay')
                ->where(array('paySn'=>$info['paySn'],'status'=>0))
                ->find();
        //订单信息不存在
        if(empty($rec)){
            log_result($file,'function ModifyOrder：支付编号对应未支付订单不存在！');
            return false;
        }
        $toatalPay = $rec['totalPay'];
        $userMoney = $rec['totalUser'];
        $totalAmount = $rec['totalAmount'];
        
        //订单金额不匹配
        if($totalAmount != ($userMoney + $toatalPay)){
            log_result($file,'function ModifyOrder：订单金额不匹配！');
            return false;
        }
        //支付金额与订单金额不匹配
        if( $info['money'] != $toatalPay){
            log_result($file,'function ModifyOrder：支付金额与订单金额不匹配！');
            return false;
        }
        M()->startTrans();
        //修改订单状态
        $data = [
            'status' => 1,  //已支付
            'payTime' => time(),
        ];
        $res = M('Orders')->where(['paySn'=>$info['paySn']])->save($data);
        if($res === false){
            log_result($file,'function ModifyOrder：修改订单状态失败！');
            M()->rollback();
            return false;
        }
        //订单后续操作
        if( !D('CartView')->afterPay($info['paySn'],$file)){
            log_result($file,'function ModifyOrder：订单支付成功后续操作执行失败！');
            M()->rollback();
            return false;
        }
        M()->commit();
        log_result($file,'function ModifyOrder：successful！');
        return true;
    }
    
}