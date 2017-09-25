<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/4 0004
 * Time: 下午 3:07
 */

namespace Admin\Controller;


class UserController extends BaseController
{
    public $db;
    function __construct()
    {
        parent::__construct();
        $this->db=M("user");
    }
    /**
     * 用户列表
     */
    public function index(){
        if(IS_POST){
            $page = I("page"); // get the requested page
            $limit = I('rows'); // get how many rows we want to have into the grid
            $sidx = I('sidx'); // get index row - i.e. user click to sort
            $sord = I('sord'); // get the direction
            if(!$sidx) $sidx ="uId";

            $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows']: false;
            if($totalrows) {
                $limit = $totalrows;
            }

            //搜索条件
            $where = '1=1';
            if($phone=$_GET['phone']){
                $where.=" and u1.phone like '%$phone%'";
            }
            if($number=$_GET['number']){
                $where.=" and u1.number like '%$number%'";
            }
            if($start=$_GET['start']){
                $start_time=strtotime($start);
                $where.=" and u1.createTime>=$start_time";
            }
            if($end=$_GET['end']){
                $end_time=strtotime($end)+24*3600-1;
                $where.=" and u1.createTime<=$end_time";
            }
            if(($uStatus=$_GET['uStatus'])!=''){
                $where.=" and u1.status=$uStatus";
            }
            $count = $this->db->alias("u1")->where($where)->count();

            if( $count >0 ) {
                $total_pages = ceil($count/$limit);
            } else {
                $total_pages = 0;
            }
            if ($page > $total_pages) $page=$total_pages;
            if ($limit<0) $limit = 0;
            $start = $limit*$page - $limit; // do not put $limit*($page - 1)
            if ($start<0) $start = 0;
            $result=$this->db->alias("u1")->join("wg_user as u2 on u1.pid=u2.id","left")->where($where)
                ->order("$sidx $sord")->field("u1.*,u2.number as pnumber")->limit($start,$limit)->select();
            foreach($result as $k=>$v){
                $result[$k]['createTime']=date("Y-m-d H:i:s",$v['createTime']);
                $result[$k]['loginTime']=$v['loginTime']?date("Y-m-d H:i:s",$v['loginTime']):"";
            }
            $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
            echo json_encode($responce);
            return ;
        }
        $this->display();
    }
    public function add(){
        $post=I("post.");
        foreach($post as $k){
            if(empty($k)){
                ajax_return_error("请将信息填写完整");
            }
        }
        $post['password']=encrypt_pass($post['password']);
        $data=D("User")->addUser($post);
        $res=$this->db->add($data);
        if($res){
            M("user")->where("id=$res")->setField("qcode",D("User")->qcode($res));
            ajax_return_ok([],"添加成功");
        }else{
            ajax_return_error("添加失败");
        }
        
        
    }

    /**
     * 修改用户状态
     */
    public function check_status(){
        $uId=I("post.id");
        $uStatus=$this->db->where("id=$uId")->getField("status");
        if($uStatus){
            $res=$this->db->where("id=$uId")->setField("status",0);
            $data=0;
        }else{
            $res=$this->db->where("id=$uId")->setField("status",1);
            $data=1;
        }
        if($res){
            $this->ajaxReturn(["status"=>1,"colname"=>"status","data"=>$data]);
        }else{
            $this->ajaxReturn(["status"=>0,"msg"=>"请稍后再试"]);
        }
    }
    /**
     * 查看用户余额
     */
    public function getMoney($id){
        $data=M("user")->where("id=$id")->find();
        $this->data=$data;
        $parents=D("User")->getParents($id);
        $this->parents=$parents;
        $this->is_admin=session("admin_auth")['isAdmin'];
        $fiel=D("User")->qcode($id);
        M("user")->where("id=$id")->setField("qcode",$fiel);
        $this->display();
    }
    /**
     * 编辑积分
     */
    public function editScore(){
        $post=I("post.");
        foreach($post as $v){
            if($v==''){
                ajax_return_error("请将信息填写完整");
            }
        }
        $admin_id=session("admin_auth")['isAdmin'];
        if($admin_id==0){
            ajax_return_error("对不起，你没有权限！");
        }
        $before=M("user")->where(["id"=>$post['uid']])->getField("score");
        $post['beforeScore']=$before;
        $model=M();
        $model->startTrans();
        if($post['is_add']==0){
            $post['cscore']=-$post['cscore'];
            $post['afterScore']=$before-$post['cscore'];
            $res1=M("user")->where(["id"=>$post['uid']])->setDec("score",$post['cscore']);
        }else{
            $post['afterScore']=$before+$post['cscore'];
            $res1=M("user")->where(["id"=>$post['uid']])->setInc("score",$post['cscore']);
        }
        $post['type']=4;
        $post['createTime']=time();
        $res=M("user_score")->add($post);
        if($res&&$res1){
            $model->commit();
            ajax_return_ok([],"提交成功");
        }else{
            $model->rollback();
            ajax_return_error("操作失败");
        }
    }
    /**
     * 编辑钱包
     */
    public function editMoney(){
        $post=I("post.");
        foreach($post as $v){
            if($v==''){
                ajax_return_error("请将信息填写完整");
            }
        }
        $post['adminId']=session("admin_auth")['uid'];
        $post['createTime']=time();
        $res=M("user_recharge")->add($post);
        if($res){
            ajax_return_ok([],"提交成功，等待超级管理员审核");
        }else{
            ajax_return_error("提交失败");
        }
    }
    /**
     * 钱包记录
     */
    public function money($uid){
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
            $where=" uid=$uid";
            $count = M("user_money")->where($where)->count();

            if( $count >0 ) {
                $total_pages = ceil($count/$limit);
            } else {
                $total_pages = 0;
            }
            if ($page > $total_pages) $page=$total_pages;
            if ($limit<0) $limit = 0;
            $start = $limit*$page - $limit; // do not put $limit*($page - 1)
            if ($start<0) $start = 0;
            $result=M("user_money")->where($where)
                ->order("$sidx $sord")->limit($start,$limit)->select();
            foreach($result as $k=>$v){
                $result[$k]['createTime']=date("Y-m-d H:i:s",$v['createTime']);
                if($v['adminId']){
                    $result[$k]['admin']=M("admin")->where("id=".$v['adminId'])->getField("username");
                }
                if($v['shopId']){
                    $result[$k]['shop']=M("shop")->where("id=".$v['shopId'])->getField("username");
                }
            }
            $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
            echo json_encode($responce);
        }else{
            $this->uid=$uid;
            $this->display();
        }
    }
    /**
     * 积分记录
     */
    public function score($uid){
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
            $where=" uid=$uid";
            $count = M("user_score")->where($where)->count();

            if( $count >0 ) {
                $total_pages = ceil($count/$limit);
            } else {
                $total_pages = 0;
            }
            if ($page > $total_pages) $page=$total_pages;
            if ($limit<0) $limit = 0;
            $start = $limit*$page - $limit; // do not put $limit*($page - 1)
            if ($start<0) $start = 0;
            $result=M("user_score")->where($where)
                ->order("$sidx $sord")->limit($start,$limit)->select();
            foreach($result as $k=>$v){
                $result[$k]['createTime']=date("Y-m-d H:i:s",$v['createTime']);
            }
            $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
            echo json_encode($responce);
        }else{
            $this->uid=$uid;
            $this->display();
        }
    }
    /**
     * 金币记录
     */
    public function gold($uid){
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
            $where=" uid=$uid";
            $count = M("wheel_record")->where($where)->count();

            if( $count >0 ) {
                $total_pages = ceil($count/$limit);
            } else {
                $total_pages = 0;
            }
            if ($page > $total_pages) $page=$total_pages;
            if ($limit<0) $limit = 0;
            $start = $limit*$page - $limit; // do not put $limit*($page - 1)
            if ($start<0) $start = 0;
            $result=M("wheel_record")->where($where)
                ->order("$sidx $sord")->limit($start,$limit)->select();
            foreach($result as $k=>$v){
                $result[$k]['createTime']=date("Y-m-d H:i:s",$v['createTime']);
            }
            $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
            echo json_encode($responce);
        }else{
            $this->uid=$uid;
            $this->display();
        }
    }
    
}