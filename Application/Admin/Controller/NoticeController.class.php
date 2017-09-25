<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/20 0020
 * Time: 上午 9:43
 */

namespace Admin\Controller;


class NoticeController extends BaseController
{
    public $db;
    public function __construct()
    {
        parent::__construct();
        $this->db=M("notice");
    }
    public function index(){
        if(!IS_POST){
            $this->display();
            return ;
        }
        $page=I("page"); // get the requested page
        $limit = I('rows'); // get how many rows we want to have into the grid
        $sidx = I('sidx'); // get index row - i.e. user click to sort
        $sord = I('sord'); // get the direction
        if(!$sidx) $sidx ="id";

        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows']: false;
        if($totalrows) {
            $limit = $totalrows;
        }

        //搜索条件
        // $where = '1=1 ';
        $where = 'sendId = 0 ';
        

        $count = $this->db->where($where)->count();

        if( $count >0 ) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        if ($limit<0) $limit = 0;
        $start = $limit*$page - $limit; // do not put $limit*($page - 1)
        if ($start<0) $start = 0;

        $result=$this->db->where($where)->order("$sidx $sord")->limit($start,$limit)->select();
        foreach($result as $k=>$v){
            $result[$k]['createTime']=date("Y-m-d H:i:s",$v['createTime']);

        }
        $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
        echo json_encode($responce);
    }
    /**
     * 修改状态
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
     * 添加
     */
    public function add(){
        if(IS_POST){
            $post=I("post.","","trim");
            foreach($post as $v){
                if($v==''){
                    ajax_return_error("请将信息填写完整");
                }
            }
            $post['createTime']=time();
            $post['updateTime']=time();
            $res=$this->db->add($post);
            if($res){
                ajax_return_ok([],"添加成功");
            }else{
                ajax_return_error("添加失败");
            }
        }else{
            $this->display();
        }
    }
    public function edit(){
        if(IS_POST){
            $post=I("post.","","trim");
            foreach($post as $v){
                if($v==''){
                    ajax_return_error("请将信息填写完整");
                }
            }
            $post['updateTime']=time();
            $res=$this->db->save($post);
            if($res){
                ajax_return_ok([],"编辑成功");
            }else{
                ajax_return_error("编辑失败");
            }
        }else{
            $nId=I("nId","","trim");
            $data=$this->db->find($nId);
            $this->data=$data;
            $this->display();
        }
    }
    public function del(){
        $id=I("nId","","trim");
        $res=$this->db->delete($id);
        if($res){
            ajax_return_ok([],"删除成功");
        }else{
            ajax_return_error("删除失败");
        }
    }
    public function send(){
        $id = I("post.nid","","trim");
        $notice=M("notice")->find($id);
        $this->addPushMsg("平台公告",$notice['title'],$notice['type']);
        $this->db->where("id=".$id)->save(["isPush"=>1,"pushTime"=>time()]);
        ajax_return_ok([],"推送成功");
    }
    //消息推送
    public function addPushMsg($title,$context,$type){

        $andChanelId = [];
        if($type == 0){
            $uChannels = M('User')->where(['status'=>1,'uAndChannel'=>['neq','']])->getField('uAndChannel',true);
            $sChannels = M('Shop')->where(['status'=>1,'andChannel'=>['neq','']])->getField('andChannel',true);
            $andChanelId = array_merge($uChannels,$sChannels);
        }elseif($type == 1){
            $andChanelId = M('User')->where(['status'=>1,'uAndChannel'=>['neq','']])->getField('uAndChannel',true);
        }elseif($type == 2){
            $andChanelId = M('Shop')->where(['status'=>1,'andChannel'=>['neq','']])->getField('andChannel',true);
        }
       
        $andChanelId = array_filter($andChanelId);
        $andChanelId = implode(',',$andChanelId);


        // $iosChanelId = array_filter($iosChanelId);
        // $iosChanelId = implode(',',$iosChanelId);


        $msg = array();
        $msg['ticker'] = "唯公商城";
        $msg['title'] = $title;
        $msg['text'] = $context;
        // $msg['ktype'] = 1;
        if( !empty($andChanelId) ) {
            // $rsAnd =  sendAndroidListcast($andChanelId,$msg);//安卓推送
            $utype = 'listcast';
            if($type == 0) $utype = 'broadcast';
            sendAnd($andChanelId,$msg,'',$utype);
        }

        // if( !empty($iosChanelId) ) {
        //     $rs =  sendIOSListcast($iosChanelId,$msg);    //IOS推送
        // }
    }
    /**
     * 信息反馈
     */
    public function feedback(){
        if(!IS_POST){
            $this->display();
            return ;
        }
        $page=I("page"); // get the requested page
        $limit = I('rows'); // get how many rows we want to have into the grid
        $sidx = I('sidx'); // get index row - i.e. user click to sort
        $sord = I('sord'); // get the direction
        if(!$sidx) $sidx ="fId";

        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows']: false;
        if($totalrows) {
            $limit = $totalrows;
        }

        //搜索条件
        $where = '1=1 ';

        $count = M("feedback")->where($where)->count();

        if( $count >0 ) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        if ($limit<0) $limit = 0;
        $start = $limit*$page - $limit; // do not put $limit*($page - 1)
        if ($start<0) $start = 0;

        $result=M("feedback")->where($where)->order("$sidx $sord")->limit($start,$limit)->select();
        foreach($result as $k=>$v){
            $result[$k]['fAddTime']=date("Y-m-d H:i:s",$v['fAddTime']);

        }
        $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
        echo json_encode($responce);
    }
    public function feed(){
        $User = M('feedback'); // 实例化User对象
        $count      = $User->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $User->join("jj_user as u on u.uId=jj_feedback.uId")->field("jj_feedback.*,u.uHeadImg")->order('fId desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }
    public function check(){
        $id=I("post.id");
        $res=M("feedback")->where("fId=$id")->setField("fState",1);
        if($res){
            ajax_return_ok([],"处理成功");
        }else{
            ajax_return_error("处理失败");
        }
    }
}