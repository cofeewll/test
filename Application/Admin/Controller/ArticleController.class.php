<?php
namespace Admin\Controller;


class ArticleController extends BaseController
{
    //首页
    public function index(){
        $this->dataUrl = U('Article/index');
        $this->addUrl = '/index.php/Admin/Article/add';
        $this->editUrl = U('Article/editRow');
        $this->showUrl = U('Article/changeStatus');
        $this->sortUrl = U('Article/changeSort');
        $this->delUrl = U('Article/del');
        if(IS_AJAX){
            //查询参数
            $title = I('title','','trim');
            $type = I('type','','trim');
            $status = I('status','','trim');

            //查询条件拼接
            if( $title !='' ) {
                $where['title'] = array('like','%'.$title.'%');
            }

            if( $status !='' ) {
                $where['status'] = $status;
            }

            if( $type !='' ) {
                $where['type'] = $type;
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
            $db = M('article');
            $lists = $db->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();

            //数据处理
            foreach ($lists as $key => $val){
                if( $val['type'] == 1) {
                    $lists[$key]['type'] = '帮助中心';
                } else if( $val['type'] == 2 ) {
                    $lists[$key]['type'] = '其它';
                }
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

    //添加
    public function add(){
         if(IS_POST){
            $addRes = D('Article')->update();
            if(!$addRes){
                $this->wrong(D('Article')->getError());
            }else{
                $this->ok('操作成功');
            }
        }else{
            $id = I('id',0,'intval');
            if($id){
                $info = M('Article')->where(['id'=>$id])->find();
                $this->info = $info;
            }
        }
        $this->display();
    }

   
    /**
     * [changeStatus 修改状态]
     * @return [type] [description]
     */
    public function changeStatus(){
        $res = D('Article')->changeStatus();
        if(!$res){
            $this->wrong(D('Article')->getError());
        }else{
            $this->ok('操作成功');
        }
    }
    /**
     * [changeStatus 修改排序]
     * @return [type] [description]
     */
    public function changeSort(){
        $res = D('Article')->changeSort();
        if(!$res){
            $this->wrong(D('Article')->getError());
        }else{
            $this->ok('操作成功');
        }
    }

    //删除
    public function del(){
        $delRes = D('Article')->delRec();
        if(!$delRes){
            $this->wrong(D('Article')->getError());
        }else{
            $this->ok('操作成功');
        }
    }

     //编辑表格
    public function editRow(){
        $id = I('id',0,'intval');
        $data['title']  = I('title','','trim');
        $data['status'] = 1;
        $rec = M('Article')->where(['title'=>$data['title']])->find();
        if($rec && $rec['id']!= $id){
            $this->wrong('标题已存在');
        }
        $where['id'] = $id;

        $updateResult = M('Article')->where($where)->save( $data );

        if( $updateResult > 0 ) {
            $this->ok('保存成功');
        } else {
            $this->wrong( '保存失败' );
        }
    }

}