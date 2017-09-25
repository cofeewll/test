<?php 
namespace Admin\Controller;
use Think\Controller;


class PublicController extends Controller {

    //自动关闭未付款的订单  改为超时未支付
    public function autoCloseOrder(){

        $where['oIsPay'] = 0;       //0=未支付 1=已支付 2=超时未支付 3=退款
        $where['oStatus'] = 1;       //1=正常 0=禁用
        $endTime = time() - 6*60;
        $where['oAddTime'] = array('lt',$endTime);

        $data['oIsPay'] = 2;
        $data['oUpdateTime'] = time();        

        $list = M('orders')->where( $where )->field('dId,oId')->select();
        M('orders')->where( $where )->save( $data ); 
        $dId = '';
        foreach ($list as $key => $val) {
            $dId  .= $val['dId'].','; 
        }
        if( !empty($dId) ) {
            $dId = substr($dId,0,-1);
            $whereDevice['dId'] = array('in',$dId);
            
            M('device')->where( $whereDevice )->setField('dState',1); 
        }
        die('ok');
    }


    //自动完成使用中的订单
    public function autoFinishOrder(){
        
        $where['oState'] = 2;       //0=未支付 1=已支付 2=超时未支付 3=退款
        $where['oStatus'] = 1;       //1=正常 0=禁用
        $endTime = time() - 5*60;
        $where['oEndTime'] = array('lt',$endTime);

        $data['oState'] = 2;
        $data['oUpdateTime'] = time();
        
        
        $list = M('orders')->where( $where )->field('dId,oId')->select();

        M('orders')->where( $where )->save( $data );

        $dataDevice['dState'] = 1;
        $dataDevice['dUpdateTime'] = time();

        foreach ($list as $key => $val) {
            M('device')->where(array('dId'=>$val['dId']))->save( $dataDevice );
        }

        //添加运行记录
        $dataAuto['asType'] = 2;
        $dataAuto['asAddTime'] = time();
        $dataAuto['asContext'] = json_encode( $list );
        M('auto_status')->add( $dataAuto );
    }

}
