<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/6 0006
 * Time: 下午 4:22
 */

namespace Shop\Controller;


use Common\Model\ShopModel;

class FinanceController extends BaseController
{
    /**
     * 设置提现账户
     */
    public function index(){
        $sid=$this->sid;
        if(IS_POST){
            
        }else{
            $shop=M("shop")->where("id=$sid")->find();
            $shop['bank']=explode("|",$shop['bank']);
            $this->data=$shop;
            $this->display();
        }
    }
    public function setBank(){
        $post=I("post.");
        $sBankAccount=array_filter($post['bank']);
        if($sBankAccount){
            $post['bank']=join("|",$sBankAccount);
        }else{
            $post['bank']='';
        }
        $res=M("shop")->save($post);
        if($res!==false){
            ajax_return_ok([],"保存成功");
        }else{
            ajax_return_error("保存失败");
        }
    }
    /**
     * 我的提现列表
     */
    public function withdraw(){
        $sid=$this->sid;
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
            $where = '1=1 and shopId='.$sid;

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
    /**
     * 申请提现
     */
    public function apply(){
        $sid=$this->sid;
        $model=new ShopModel();
        $money=$model->getMoney($sid);
        if(IS_POST){
            $post=I("post.");
            foreach($post as $v){
                if($v==''){
                    ajax_return_error("请将信息填写完整");
                }
            }

            if($money<$post['amount']){
                ajax_return_error("你的钱包余额不足");
            }
            if($post['amount']<=0){
                ajax_return_error("请输入有效金额");
            }
            $post['addTime']=time();
            $res=M("shop_withdraw")->add($post);
            if($res){
                ajax_return_ok([],"提交成功");
            }else{
                ajax_return_error("提交失败");
            }
        }else{
            $this->data=M("shop")->where("id=$sid")->find();
            $this->money=$money;
            $this->display();
        }
    }
    public function getAccount(){
        $sid=$this->sid;
        $type=I("post.type");
        $shop=M("shop")->where("id=$sid")->find();
        if($type==1){
            $account=$shop['alipay'];
        }
        if($type==2){
            $account=$shop['wechat'];
        }
        if($type==3){
            $account=explode("|",$shop['bank']);
        }
        ajax_return_ok($account);
    }
    /**
     * 我的补偿金列表
     */
    public function compen(){
        $sid=$this->sid;
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
            $where = 'type=1 and shopId='.$sid;

            $count = M("user_money")->alias("um")->join("wg_user as u on u.id=um.uid","left")
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
            $result=M("user_money")->alias("um")->join("wg_user as u on u.id=um.uid","left")
                ->where($where)->field("um.*,u.number")->order("$sidx $sord")
                ->limit($start,$limit)->select();
            foreach($result as $k=>$v){
                $result[$k]['createTime']=date("Y-m-d H:i:s",$v['createTime']);
            }
            $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
            echo json_encode($responce);
        }else{
            $this->display();
        }
    }
}