<?php
namespace Admin\Controller;


class BannerController extends BaseController
{
    //轮播图列表
    public function index(){
        $this->dataUrl = U('Banner/index');
        $this->addUrl = '/index.php/Admin/Banner/add';
        $this->delUrl = U('Banner/del');
        $this->sortUrl = U('Banner/changeSort');
        $this->editUrl = U('Banner/editRow');
        $this->showUrl = U('Banner/changeStatus');
        if(IS_AJAX){
            //查询参数
            $title = I('title','','trim');
            $status = I('status');

            //查询条件拼接
            if( $title !='' ) {
                $where['title'] = array('like','%'.$title.'%');
            }

            if( $status !='' ) {
                $where['status'] = $status;
            }

            $where= empty($where) ? true: $where;

            //分页参数
            $length = I('rows',10,'intval');   //每页条数
            $page = I('page',1,'intval');      //第几页
            $start = ($page - 1) * $length;     //分页开始位置

            //排序
            $sortRow = I('sidx','sorts','trim');      //排序列
            $sort = I('sord','asc','trim');        //排序方式
            $order = $sortRow.' '.$sort;

            //数据查询
            $db = M('banner');
            $lists = $db->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();

            //数据处理
            foreach ($lists as $key => $val){
                $lists[$key]['addTime']=date("Y-m-d H:i:s",$val['addTime']);
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

    //双击编辑标题
    public function editRow(){
        $id = I('id',0,'intval');
        $data['title']  = I('title','','trim');
        $data['updateTime'] = time();
        $rec = M('banner')->where(['title'=>$data['title']])->find();
        if($rec && $rec['id']!= $id){
            $this->wrong('标题已存在');
        }
        $where['id'] = $id;

        $updateResult = M('banner')->where($where)->save( $data );

        if( $updateResult > 0 ) {
            $this->ok('保存成功');
        } else {
            $this->wrong( '保存失败' );
        }
    }

    //添加
    public function add(){
        if(IS_POST){
            $addRes = D('Banner')->update();
            if(!$addRes){
                $this->wrong(D('Banner')->getError());
            }else{
                $this->ok('操作成功');
            }
        }else{
            $id = I('id',0,'intval');
            if($id){
                $info = M('Banner')->where(['id'=>$id])->find();
                $this->info = $info;
            }
        }
        $this->display();
    }

    //修改排序
    public function changeSort(){
        $delRes = D('Banner')->changeSort();
        if(!$delRes){
            $this->wrong(D('Banner')->getError());
        }else{
            $this->ok('操作成功');
        }
    }
    //修改状态
    public function changeStatus(){
        $delRes = D('Banner')->changeStatus();
        if(!$delRes){
            $this->wrong(D('Banner')->getError());
        }else{
            $this->ok('操作成功');
        }
    }

    //删除
    public function del(){
		$delRes = D('Banner')->delRec();
        if(!$delRes){
            $this->wrong(D('Banner')->getError());
        }else{
            $this->ok('操作成功');
        }
	}

}