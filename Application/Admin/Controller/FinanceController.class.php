<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/1 0001
 * Time: 上午 9:50
 */

namespace Admin\Controller;


class FinanceController extends BaseController
{
    /**
     * 充值审核列表
     */
    public function index(){
        if(IS_POST){
            $page = I("page"); // get the requested page
            $limit = I('rows'); // get how many rows we want to have into the grid
            $sidx = I('sidx'); // get index row - i.e. user click to sort
            $sord = I('sord'); // get the direction
            if(!$sidx) $sidx ="id";

            $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows']: false;
            if($totalrows) {
                $limit = $totalrows;
            }

            //搜索条件
            $where = '1=1';
            if($phone=$_GET['phone']){
                $where.=" and u.phone like '%$phone%'";
            }

            if($start=$_GET['start']){
                $start_time=strtotime($start);
                $where.=" and ur.createTime>=$start_time";
            }
            if($end=$_GET['end']){
                $end_time=strtotime($end)+24*3600-1;
                $where.=" and ur.createTime<=$end_time";
            }
            if(($uStatus=$_GET['status'])!=''){
                $where.=" and ur.status=$uStatus";
            }
            $count = M("user_recharge")->alias("ur")
                ->join("wg_user as u on ur.uid=u.id","left")
                ->join("wg_admin as a on a.id=ur.adminId","left")->where($where)->count();
            if( $count >0 ) {
                $total_pages = ceil($count/$limit);
            } else {
                $total_pages = 0;
            }
            if ($page > $total_pages) $page=$total_pages;
            if ($limit<0) $limit = 0;
            $start = $limit*$page - $limit; // do not put $limit*($page - 1)
            if ($start<0) $start = 0;
            $result=M("user_recharge")->alias("ur")
                ->join("wg_user as u on ur.uid=u.id","left")
                ->join("wg_admin as a on a.id=ur.adminId","left")->where($where)
                ->order("$sidx $sord")->field("ur.*,number,phone,username")->limit($start,$limit)->select();
            foreach($result as $k=>$v){
                $result[$k]['createTime']=date("Y-m-d H:i:s",$v['createTime']);

            }
            $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
            echo json_encode($responce);
        }else{
            $this->display();
        }
    }
    public function check(){
        $post=I("post.");
        $model=M();
        $model->startTrans();
        $res=M("user_recharge")->save($post);
        if($post['status']==1){
            //同意充值
            $data=M("user_recharge")->find($post['id']);
            $money=M("user")->where(["id"=>$data['uid']])->getField("money");
            if($data['is_add']==0){
                $cmoney=-$data['cmoney'];
                $res1=M("user")->where(["id"=>$data['uid']])->setDec("money",$data['cmoney']);
            }else{
                $cmoney=$data['cmoney'];
                $res1=M("user")->where(["id"=>$data['uid']])->setInc("money",$data['cmoney']);
            }
            //加入用户钱包明细
            $map['uid']=$data['uid'];
            $map['type']=2;
            $map['cmoney']=$cmoney;
            $map['beforeMoney']=$money;
            $map['afterMoney']=$money+$cmoney;
            $map['createTime']=time();
            $map['adminId']=$data['adminId'];
            $res2=M("user_money")->add($map);
        }else{
            $res1=1;
            $res2=1;
        }
        if($res&&$res1&&$res2){
            $model->commit();
            ajax_return_ok([],"操作成功");
        }else{
            $model->rollback();
            ajax_return_error("操作失败");
        }
    }
    /**
     * 商家结算
     */
    public function shop(){
        if(IS_POST){
            $page = I("page"); // get the requested page
            $limit = I('rows'); // get how many rows we want to have into the grid
            $sidx = I('sidx'); // get index row - i.e. user click to sort
            $sord = I('sord'); // get the direction
            if(!$sidx) $sidx ="id";

            $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows']: false;
            if($totalrows) {
                $limit = $totalrows;
            }

            //搜索条件
            $where = '1=1';
            if($get=$_GET){
                if($v=$get['title']){
                    $where.=" and title like '%$v%'";
                }
            }
            $count = M("shop")->where($where)->count();
            if( $count >0 ) {
                $total_pages = ceil($count/$limit);
            } else {
                $total_pages = 0;
            }
            if ($page > $total_pages) $page=$total_pages;
            if ($limit<0) $limit = 0;
            $start = $limit*$page - $limit; // do not put $limit*($page - 1)
            if ($start<0) $start = 0;
            $result=M("shop")
                ->where($where)
                ->limit($start,$limit)->select();
            foreach($result as $k=>$v){
                $result[$k]['setMoney']=M("orders")->where("shopId=".$v['id'])->sum("setMoney");
                $result[$k]['total_withdraw']=M("shop_withdraw")->where("shopId=".$v['id']." and status=1")->sum("amount")?:"0.00";
                $result[$k]['compen_money']=M("user_money")->where("shopId=".$v['id']." and type=1")->sum("cmoney")?:"0.00";
            }
            $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
            echo json_encode($responce);
        }else{
            $this->display();
        }
    }
    /**
     * 提现列表
     */
    public function withdraw(){
        if(IS_POST){
            $page = I("page"); // get the requested page
            $limit = I('rows'); // get how many rows we want to have into the grid
            $sidx = I('sidx'); // get index row - i.e. user click to sort
            $sord = I('sord'); // get the direction
            if(!$sidx) $sidx ="id";

            $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows']: false;
            if($totalrows) {
                $limit = $totalrows;
            }

            //搜索条件
            $where = '1=1';

            $count = M("shop_withdraw")->alias("sw")->join("wg_shop as s on s.id=sw.shopId","left")
                ->where($where)->count();
            if( $count >0 ) {
                $total_pages = ceil($count/$limit);
            } else {
                $total_pages = 0;
            }
            if ($page > $total_pages) $page=$total_pages;
            if ($limit<0) $limit = 0;
            $start = $limit*$page - $limit; // do not put $limit*($page - 1)
            if ($start<0) $start = 0;
            $result=M("shop_withdraw")->alias("sw")->join("wg_shop as s on s.id=sw.shopId","left")
                ->where($where)->field("sw.*,s.username")->order("$sidx $sord")
                ->limit($start,$limit)->select();
            foreach($result as $k=>$v){
                $result[$k]['addTime']=date("Y-m-d H:i:s",$v['addTime']);
            }
            $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
            echo json_encode($responce);
        }else{
            $this->display();
        }
    }
    public function sdetail($id){
        $data=M("shop_withdraw")->where("id=$id")->find();
        $data['admin_name']=M("admin")->where("id=".$data['actionId'])->getField("username");
        $this->username=session("admin_auth")['username'];
        $shop=M("shop")->where("id=".$data['shopId'])->find();
        $this->data=$data;
        $this->shop=$shop;
        $this->display();
    }
    public function checkServer(){
        $post=I("post.");
        $post['auditTime']=time();
        $post['actionId']=$_SESSION['admin_auth']['uid'];
        $res=M("shop_withdraw")->save($post);

        if($res){
            ajax_return_ok([],"处理成功");
        }else{
            ajax_return_error("处理失败");
        }
    }
    /**
     * 查看补偿金
     */
    public function compen($id){
        if(IS_POST){
            $page = I("page"); // get the requested page
            $limit = I('rows'); // get how many rows we want to have into the grid
            $sidx = I('sidx'); // get index row - i.e. user click to sort
            $sord = I('sord'); // get the direction
            if(!$sidx) $sidx ="id";

            $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows']: false;
            if($totalrows) {
                $limit = $totalrows;
            }

            //搜索条件
            $where = 'um.type=1 and um.shopId='.$id;

            $count = M("user_money")->alias("um")->join("wg_user as u on u.id=um.uid")
                ->where($where)->count();
            if( $count >0 ) {
                $total_pages = ceil($count/$limit);
            } else {
                $total_pages = 0;
            }
            if ($page > $total_pages) $page=$total_pages;
            if ($limit<0) $limit = 0;
            $start = $limit*$page - $limit; // do not put $limit*($page - 1)
            if ($start<0) $start = 0;
            $result=M("user_money")->alias("um")->join("wg_user as u on u.id=um.uid")->field("um.*,u.number")
                ->where($where)->order("$sidx $sord")
                ->limit($start,$limit)->select();
            foreach($result as $k=>$v){
                $result[$k]['createTime']=date("Y-m-d H:i:s",$v['createTime']);
            }
            $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
            echo json_encode($responce);
        }else{
            $this->id=$id;
            $this->display();
        }
    }
}