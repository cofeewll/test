<?php
namespace ShopApi\Controller;

/**
* 首页接口
*/
class IndexController extends CenterController
{
	
	/**
	 * 获取首页基本信息
	 * @return [type] [description]
	 */
	public function getInfo(){
		$sid = $this->sid;
		$shop = M('shop')->where(['id'=>$sid])->field('id,phone,title,username,img')->find();
		if($shop['img']){
			$shop['img'] = "http://".$_SERVER['HTTP_HOST'].$shop['img'];
		}else{
			$default = M('config')->where(['config'=>'avatar','flag'=>1])->getField('value');
			$shop['img'] = "http://".$_SERVER['HTTP_HOST'].$default;
		}
		
		$year = date('Y');
		$month = date('m');
		$day = date('d');
		$info = [];
		//订单相关数据
		$data = M('Orders')->where(['shopId'=>$sid,'status'=>['in','1,2,3,5']])->field('sum(amount) as amount,count(*) as num')->find();
		$dataY = M('Orders')->where(['shopId'=>$sid,'status'=>['in','1,2,3,5'],'oYear'=>$year])->field('sum(amount) as amount,count(*) as num')->find();
		$dataM = M('Orders')->where(['shopId'=>$sid,'status'=>['in','1,2,3,5'],'oYear'=>$year,'oMonth'=>$month])->field('sum(amount) as amount,count(*) as num')->find();
		$dataD = M('Orders')->where(['shopId'=>$sid,'status'=>['in','1,2,3,5'],'oYear'=>$year,'oMonth'=>$month,'oDate'=>$day])->field('sum(amount) as amount,count(*) as num')->find();
		//售后相关数据
		$ys = strtotime(date('Y-01-01'));
		$ye = strtotime(date('Y-12-31 23:59:59'));
		$ms = strtotime(date('Y-m-01'));
		$me = strtotime(date('Y-m-t 23:59:59'));
		$ds = strtotime(date('Y-m-d'));
		$de = strtotime(date('Y-m-d 23:59:59'));
		$rdata = M('OrderRefund')->where(['shopId'=>$sid,'status'=>1])->field('sum(realMoney) as amount,count(*) as num')->find();
		$rdataY = M('OrderRefund')->where(['shopId'=>$sid,'status'=>1,'createTime'=>['between',[$ys,$ye]]])->field('sum(realMoney) as amount,count(*) as num')->find();
		$rdataM = M('OrderRefund')->where(['shopId'=>$sid,'status'=>1,'createTime'=>['between',[$ms,$me]]])->field('sum(realMoney) as amount,count(*) as num')->find();
		$rdataD = M('OrderRefund')->where(['shopId'=>$sid,'status'=>1,'createTime'=>['between',[$ds,$de]]])->field('sum(realMoney) as amount,count(*) as num')->find();
		$info = [
			'orderTotalAmount' => $data['amount']?$data['amount']:"0.00",
			'orderTotalNum' => $data['num']?$data['num']:"0",
			'orderYearAmount' => $dataY['amount']?$dataY['amount']:"0.00",
			'orderYearNum' => $dataY['num']?$dataY['num']:"0",
			'orderMonthAmount' => $dataM['amount']?$dataM['amount']:"0.00",
			'orderMonthNum' => $dataM['num']?$dataM['num']:"0",
			'orderDayAmount' => $dataD['amount']?$dataD['amount']:"0.00",
			'orderDayNum' => $dataD['num']?$dataD['num']:"0",
			'refundTotalAmount' => $rdata['amount']?$rdata['amount']:"0.00",
			'refundTotalNum' => $rdata['num']?$rdata['num']:"0",
			'refundYearAmount' => $rdataY['amount']?$rdataY['amount']:"0.00",
			'refundYearNum' => $rdataY['num']?$rdataY['num']:"0",
			'refundMonthAmount' => $rdataM['amount']?$rdataM['amount']:"0.00",
			'refundMonthNum' => $rdataM['num']?$rdataM['num']:"0",
			'refundDayAmount' => $rdataD['amount']?$rdataD['amount']:"0.00",
			'refundDayNum' => $rdataD['num']?$rdataD['num']:"0",
		];

		$data = ['shop'=>$shop,'data'=>$info];
		ajax_return_ok($data);
	}

	

    /**
     * 消息列表
     */
    public function notice(){
        $sid = $this->sid;
        $data = M("notice")->where(['type'=>['in','0,2'],'_string'=>'sendId = 0 or sendId ='.$sid,'status'=>1])->order("id desc")->select();
        foreach($data as $k=>$v){
            $content = str_replace('src="/Uploads','src="http://'.$_SERVER['HTTP_HOST'].'/Uploads',htmlspecialchars_decode($v['content']));
            $content = str_replace('<img','<img style="width:100%;" ',$content);
            $data[$k]['content'] = $content;
            $find = M("notice_read")->where("nid={$v['id']} and uid=$sid and type=2")->find();
            if($find){
                $data[$k]['is_read']=1;
            }else{
                $data[$k]['is_read']=0;
            }
            $data[$k]['brief']=strip_tags(htmlspecialchars_decode($v['content']));
        }
        ajax_return_ok($data,"请求成功");
    }
    /**
     * 消息由未读变成已读
     */
    public function sysRead(){
        $id = I("post.id",0,'intval');
        if(!$id){
        	ajax_return_error("",9);
        }
        $sid = $this->sid;
        if( !M('notice')->where(['id'=>$id])->find()){
        	ajax_return_error("信息不存在");
        }
        if(M("notice_read")->where("nid=$id and uid=$sid and type=2")->find()){
            ajax_return_ok([],"请求成功");
        }else{
            $res = M("notice_read")->add(["nid"=>$id,"uid"=>$sid,"createTime"=>time(),"type"=>2]);
            if($res){
                ajax_return_ok([],"修改成功");
            }else{
                ajax_return_error("修改失败");
            }
        }

    }
    /**
     * 判断是否有未读消息
     */
    public function hasNoRead(){
        $sid = $this->sid;
        $arr1 = M("notice")->where(['type'=>['in','0,2'],'_string'=>'sendId = 0 or sendId ='.$sid,'status'=>1])->getField("id");
        $arr2 = M("notice_read")->where("uid=$sid and type=2")->getField("nid",true);
        $res = array_diff($arr1,$arr2);
        if($res){
            ajax_return_ok(["has_no_read"=>1]);
        }else{
            ajax_return_ok(["has_no_read"=>0]);
        }
    }

    /**
     * 登出
     * 已知bug：登出的附加操作依赖session中的用户缓存，而logout方法自身并不提供用户缓存，因此这并不总是有效。
     */
    public function logout() {
        // 当前用户缓存删除
        session ( C( "SESSION_NAME_CUR_SHOP" ), null );

        ajax_return_ok();
    }
}