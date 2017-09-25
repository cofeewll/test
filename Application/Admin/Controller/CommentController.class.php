<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7 0007
 * Time: 下午 1:47
 */

namespace Admin\Controller;


class CommentController extends BaseController
{
    public $db;
    public function __construct()
    {
        parent::__construct();
        $this->db=M("order_comment");
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
        $get=$_GET;
        $where=$this->getWhere($get);
        $count = $this->db->alias("oc")->join("wg_orders as o on o.id=oc.orderId","left")
            ->join("wg_user as u on u.id=oc.uid","left")
            ->join("wg_shop as s on s.id=oc.shopId","left")
            ->join("wg_goods as g on g.id=oc.goodsId","left")
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

        $result=$this->db->alias("oc")->join("wg_orders as o on o.id=oc.orderId","left")
            ->join("wg_user as u on u.id=oc.uid","left")
            ->join("wg_shop as s on s.id=oc.shopId","left")
            ->join("wg_goods as g on g.id=oc.goodsId","left")
            ->field("oc.*,o.orderSn,u.number,s.title,g.name")->where($where)
            ->order("$sidx $sord")->limit($start,$limit)->select();

        foreach($result as $k=>$v){
            $result[$k]['createTime']=date("Y-m-d H:i:s",$v['createTime']);
        }
        $responce=array("page"=>$page,"total"=>$total_pages,"records"=>$count,"rows"=>$result);
        echo json_encode($responce);
    }
    public function getWhere($get){
        $where="1=1 ";
        foreach($get as $k=>$v){
            if($v!=''){
                if($k=='orderSn'){
                    $where.=" and o.$k like '%$v%'";
                    continue;
                }
                if($k=='name'){
                    $where.=" and g.$k like '%$v%'";
                    continue;
                }
                if($k=='number'){
                    $where.=" and u.$k like '%$v%'";
                    continue;
                }
                if($k=='title'){
                    $where.=" and s.$k like '%$v%'";
                    continue;
                }
                if($k=='start'){
                    $where.=" and createTime>=".strtotime($v);
                    continue;
                }
                if($k=='end'){
                    $where.=" and createTime<=".(strtotime($v)+24*3600-1);
                    continue;
                }
            }
        }
        return $where;
    }
    /**
     * 修改用户状态
     */
    public function check_status(){
        $uId=I("post.id");
        $uStatus=$this->db->where("id=$uId")->getField("isShow");
        if($uStatus){
            $res=$this->db->where("id=$uId")->setField("isShow",0);
            $data=0;
        }else{
            $res=$this->db->where("id=$uId")->setField("isShow",1);
            $data=1;
        }
        if($res){
            $this->ajaxReturn(["status"=>1,"colname"=>"isShow","data"=>$data]);
        }else{
            $this->ajaxReturn(["status"=>0,"msg"=>"请稍后再试"]);
        }
    }
    /**
     * 查看详情
     */
    public function detail($id){
        $data=$this->db->find($id);
        if($data['imgs']){
            $data['imgs']=explode("|",trim($data['imgs'],"|"));
        }else{
            $data['imgs']=[];
        }

        $user=M("user")->where("id=".$data['uid'])->field("number,nickname,img,aims")->find();
        $this->goods=M("goods")->where("id=".$data['goodsId'])->field("img,name")->find();
        $this->user=$user;
        $this->data=$data;
        $this->display();
    }
    public function reply(){
        $post=I("post.");
        $post['replyTime']=time();
        $res=$this->db->save($post);
        if($res){
            ajax_return_ok([],"回复成功");
        }else{
            ajax_return_error("回复失败");
        }
    }
}