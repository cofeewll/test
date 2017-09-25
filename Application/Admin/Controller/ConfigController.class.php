<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/19 0019
 * Time: 下午 3:06
 */

namespace Admin\Controller;


class ConfigController extends BaseController
{
    public $db;
    public function __construct()
    {
        parent::__construct();
        $this->db=M("config");
    }
    public function index(){
        $images = [];
        $data = $this->db->where(['flag'=>1,'status'=>1])->order('sorts asc,id asc')->select();
        foreach ($data as $k => $v) {
            if($v['type'] == 'file'){
                $temp['name'] = $v['config'];
                $temp['value'] = $v['value']?$v['value']:'';
                $images[] = $temp;
            }
        }
        $ps = $this->db->where(['config'=>['in','alipay,wxpay,kdniao,sms']])->getField('config,value');
        foreach ($ps as $key => $value) {
            $this->assign($key,unserialize($value));
        }
        // trace($images);
        $this->images = json_encode($images);
        $this->data=$data;
        $this->display();
    }
    public function edit(){
        $post=I("post.config");
        foreach($post as $k=>$v){
            $this->db->where(['config'=>$k])->setField("value",$v);
        }
        $this->ok('保存成功');
    }

    public function editConfig(){
        $post = I('post.');
        foreach ($post as $key => $value) {
            $this->db->where(['config'=>$key])->setField("value",serialize($value));
        }
        $this->ok('保存成功');
    }

    /**
     * [message 管理员消息列表]
     * @return [type] [description]
     */
    public function message(){
        $this->dataUrl = U('Config/message');
        $this->showUrl = U('Config/read');
        $this->delUrl = U('Config/del');
        if(IS_AJAX){
            //查询参数
            $title = I('title','','trim');
            $status = I('status','','trim');

            //查询条件拼接
            if( $title !='' ) {
                $where['title'] = array('like','%'.$title.'%');
            }

            if( $status !='' ) {
                if($status>0){
                    $where['readTime'] = ['gt',0];
                }else{
                    $where['readTime'] = ['eq',0];
                }
                
            }

            $where= empty($where) ? true: $where;

            //分页参数
            $length = I('rows',10,'intval');   //每页条数
            $page = I('page',1,'intval');      //第几页
            $start = ($page - 1) * $length;     //分页开始位置

            //排序
            $sortRow = I('sidx','id','trim');      //排序列
            $sort = I('sord','desc','trim');        //排序方式
            $order = $sortRow.' '.$sort;

            //数据查询
            $db = M('Message');
            $lists = $db->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();

            //数据处理
            foreach ($lists as $key => &$val){
                $val['createTime'] = date('Y-m-d H:i:s',$val['createTime']);
            }

            //数据返回
            $totalCount = $db->where($where)->count();
            $totalPage = ceil($totalCount/$length);
            $result['page'] = $page;
            $result['total'] = $totalPage;
            $result['records'] = $totalCount;
            $result['rows'] = $lists;

            $this->ajaxReturn($result);
        } else {
            $this->display();
        }
    }
    /**
     * [read 设置消息已读状态]
     * @return [type] [description]
     */
    public function read(){
        $id = I('id',0,'intval');
        $value = I('value',0,'intval');
        if($value>0){
            $data = ['readTime'=>0];
        }else{
            $data = ['readTime'=>time()];
        }
        $data['adminId'] = $_SESSION['admin_auth']['uid'];
        $res = M('message')->where(['id'=>$id])->save($data);
        if($res){
            $this->ok('操作成功');
        }else{
            $this->wrong('操作失败');
        }

    }

    public function del(){
        $id = I('id',0,'intval');
        if(!$id){
            $this->wrong('参数错误');
        }
        $info = M('message')->where(['id'=>$id])->find();
        if(empty($info)){
            $this->wrong('记录不存在');
        }
        $res = M('message')->where(['id'=>$id])->delete();
        if(!$res){
            $this->wrong('操作失败');
        }
        $this->ok('操作成功');
    }
}