<?php
namespace Admin\Controller;

/**
* 商家控制器
*/
class ShopController extends BaseController
{

	/**
	 * [index 商家列表]
	 * @return [type] [description]
	 */
	public function index(){

		$this->dataUrl = U('Shop/index');
        $this->addUrl = "/index.php/Admin/Shop/add";
        $this->showUrl = U('Shop/changeStatus');
        $this->delUrl = U('Shop/del');
        if(IS_AJAX){
            //查询参数
            $title = I('title','','trim');
            $username = I('username','','trim');
            $phone = I('phone','','trim');
            $status = I('status','','trim');

            $where = ['status'=>array('in','0,1')];
            //查询条件拼接
            if( $username !='' ) {
                $where['username'] = array('like','%'.$username.'%');
            }
            if( $title !='' ) {
                $where['title'] = array('like','%'.$title.'%');
            }

            if( $phone !='' ) {
                $where['phone'] = array('like','%'.$phone.'%');
            }
            if( $status !='' ) {
                $where['status'] = array('eq',$status);
            }

            $where= empty($where) ? true: $where;

            //分页参数
            $length = I('rows',10,'intval');   //每页条数
            $page = I('page',1,'intval');      //第几页
            $start = ($page - 1) * $length;     //分页开始位置

            //排序
            $sortRow = I('sidx','id','trim');      //排序列
            $sort = I('sord','asc','trim');        //排序方式
            $order = $sortRow.' '.$sort;

            //数据查询
            $db = M('Shop');
            $lists = $db->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();

            //数据处理
            foreach ($lists as $key => $val){
            	$lists[$key]['shopFee'] = ($val['shopFee']>0)? $val['shopFee']*100 .'%':'未设置';
                $lists[$key]['regTime'] = date('Y-m-d H:i:s',$val['regTime']);
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
	 * [add 添加、编辑商家信息]
	 */
	public function add(){
		if(IS_POST){
			//接收数据 
            M()->startTrans();       
	        $addResult = D('Shop')->update();
            $sid = $_POST['id']?$_POST['id']:$addResult;
	        if( !$addResult ) {
                M()->rollback();
	            $this->wrong(D('Shop')->getError());
	        } else {
                $res = D('Shop')->upImages($_POST['images'],$sid,2);
                if(!$res){
                    M()->rollback();
                    $this->wrong('保存失败');
                }
                M()->commit();
	            $this->ok('保存成功');
	        }
		}else{
			$id = I('id','0','int');
	        if($id){
	            $model = M('shop');
	            $info = $model->where(array('id'=>$id))->find();
	            if( empty($info) ){
	                $this->error('数据不存在');
	            }
                $images = M('Picture')->where(['sid'=>$id,'type'=>2])->getField('path',true);
	            $info['images'] = $images?implode(',', $images):'';
	            $this->assign('info',$info);
	        }
	        $this->display();
		}
	}

    /**
     * [audit 商家审核列表]
     * @return [type] [description]
     */
	public function audit(){
        $this->dataUrl = U('Shop/audit');
        $this->editUrl = U('Shop/edit');
        $this->addUrl = "/index.php/Admin/Shop/add";
        $this->showUrl = U('Shop/dealStatus');
        if(IS_AJAX){
            //查询参数
            $title = I('title','','trim');
            $username = I('username','','trim');
            $phone = I('phone','','trim');
            $status = I('status','','trim');

            $where = ['status'=>array('in','-1,2')];
            //查询条件拼接
            if( $username !='' ) {
                $where['username'] = array('like','%'.$username.'%');
            }
            if( $title !='' ) {
                $where['title'] = array('like','%'.$title.'%');
            }

            if( $phone !='' ) {
                $where['phone'] = array('like','%'.$phone.'%');
            }
            if( $status !='' ) {
                $where['status'] = array('eq',$status);
            }


            $where= empty($where) ? true: $where;

            //分页参数
            $length = I('rows',10,'intval');   //每页条数
            $page = I('page',1,'intval');      //第几页
            $start = ($page - 1) * $length;     //分页开始位置

            //排序
            $sortRow = I('sidx','id','trim');      //排序列
            $sort = I('sord','asc','trim');        //排序方式
            $order = $sortRow.' '.$sort;

            //数据查询
            $db = M('Shop');
            $lists = $db->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();

            //数据处理
            foreach ($lists as $key => $val){
                $lists[$key]['username'] = trim($val['username']) == ''? '未分配' : $val['username'];
                $lists[$key]['shopFee'] = ($val['shopFee']>0)? $val['shopFee']*100 .'%':'未设置';
                $lists[$key]['regTime'] = date('Y-m-d H:i:s',$val['regTime']);
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
     * [edit 快速编辑商家手续费]
     * @return [type] [description]
     */
    public function edit(){
        $id = I('id',0,'intval');
        $data['shopFee']   = I('shopFee','','floatval');
        $data['username'] = I('username','','trim');
        if($data['shopFee']<0 || $data['shopFee']>1){
            $this->wrong( '请填写0-1之间小数' );
        }
        if($data['username'] == ''){
            $this->wrong('请填写商家账号');
        }
        $rec = M('Shop')->where(['username'=>$data['username']])->find();
        if($rec && $rec['id'] != $id){
            $this->wrong('该账号已被使用');
        }
        $data['updateTime'] = time();
        
        $where['id'] = $id;

        $updateResult = M('Shop')->where($where)->save( $data );

        if( $updateResult > 0 ) {
            $this->ok('保存成功');
        } else {
            $this->wrong( '保存失败' );
        }
    }

	/**
	 * [changeStatus 修改商家状态]
	 * @return [type] [description]
	 */
	public function changeStatus(){
		$addResult = D('Shop')->changeStatus();

        if( !$addResult ) {
            $this->wrong(D('Shop')->getError());
        } else {
            $this->ok('编辑成功');
        }
	}
    /**
     * [dealAudit 修改审核状态]
     * @return [type] [description]
     */
    public function dealAudit(){
        $id = I('id',0,'intval');
        $value = I('value',0,'intval');
        $where = ['id'=>$id];
        $rec = M('Shop')->where($where)->find();
        if(!in_array($value, [1,-1])){
            $this->wrong('不符合规范');
        }
        
        $data = [
            'status'=>$value,
            'updateTime'=>time(),
        ];
        $content = '您的商家入驻申请被拒绝';
        if($value == 1){
            if($rec['shopFee'] == -1){
                $this->wrong('设置商家手续费比例才可通过申请');
            }
            if(trim($rec['username']) == '' ){
                $this->wrong('分配商家账号才可通过申请');
            }
            //申请通过后，分配默认密码
            $password = M('config')->where(['config'=>'password','flag'=>1])->getField('value');
            $data['password'] = encrypt_pass($password);
            $content = '您的商家入驻申请已通过,请联系平台客服咨询详细信息。';
        }
        
        $updateResult = M('Shop')->where($where)->save( $data );

        if( $updateResult > 0 ) {
            //向用户推送审核消息
            addNotice('商家入驻申请结果通知',$content,2,$rec['fromId']);
            $this->ok('操作成功');
        } else {
            $this->wrong( '操作失败' );
        }
    }
    /**
     * [del 删除商家信息]
     * @return [type] [description]
     */
    public function del(){
        $id = I('id',0,'intval');
        if(!$id){
            $this->wrong( '参数有误' );
        }
        $info = M('shop')->where(['id'=>$id])->find();
        if(empty($info)){
            $this->wrong( '信息不存在' );
        }
        $goods = M('Goods')->where(['shopId'=>$id])->count();
        if($goods){
            $this->wrong( '该商家存在商品' );
        }
        if(M('Shop')->where()->delete()){
            $this->ok('操作成功');
        }else{
            $this->wrong( '操作失败' );
        }
    }
    
    
}