<?php
namespace Admin\Controller;


class FeedbackController extends BaseController
{
    //反馈列表
    public function index(){
        $this->dataUrl = U('Feedback/index');
        $this->delUrl = U('Feedback/del');
        $this->editUrl = U('Feedback/editRow');
        $this->showUrl = U('Feedback/changeStatus');
        if(IS_AJAX){
            //查询参数
            $keyword = I('keyword','','trim');
            $status = I('status');

            //查询条件拼接
            if( $keyword !='' ) {
                $where['context'] = array('like','%'.$keyword.'%');
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
            $sortRow = I('sidx','id','trim');      //排序列
            $sort = I('sord','desc','trim');        //排序方式
            $order = $sortRow.' '.$sort;

            //数据查询
            $db = M('feedback');
            $lists = $db->alias('f')->field('f.*,number,nickname')
                    ->join('wg_user on wg_user.id = f.uid')
                    ->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();
            
            //数据处理
            foreach ($lists as $key => &$val){
                $val['uid'] = '[编号:'.$val['number'].']'.$val['nickname'];
                $val['createTime'] = date('Y-m-d H:i:s',$val['createTime']);
                $val['remark'] = $val['remark']?$val['remark']:'无';
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
     * [changeStatus 修改处理状态]
     * @return [type] [description]
     */
    public function changeStatus(){
        $res = D('Feedback')->changeStatus();
        if(!$res){
            $this->wrong(D('Feedback')->getError());
        }else{
            $this->ok('操作成功');
        }
    }

    //删除
    public function del(){
		$delRes = D('Feedback')->delRec();
        if(!$delRes){
            $this->wrong(D('Feedback')->getError());
        }else{
            $this->ok('操作成功');
        }
	}

     //编辑表格
    public function editRow(){
        $id = I('id',0,'intval');
        $data['remark']  = I('remark','','trim');
        $data['status'] = 1;
        
        $where['id'] = $id;

        $updateResult = M('feedback')->where($where)->save( $data );

        if( $updateResult > 0 ) {
            $this->ok('保存成功');
        } else {
            $this->wrong( '保存失败' );
        }
    }

    
}