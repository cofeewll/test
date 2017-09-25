<?php
namespace Admin\Controller;
/**
* 转盘管理
*/
class WheelController extends BaseController
{
	public $privilege = array('prize'=>['addPrize','editPrize','changeStatus','changeSort','delPrize'],
                'config'=>[],
                'record'=>['editRecord','delRecord'],
                'notices'=>['editNotice','editRow','delNotice','updateStatus'],
                );
	/**
	 * [prize 奖品列表]
	 * @return [type] [description]
	 */
	public function prize(){
		$this->dataUrl = U('Wheel/prize');
        $this->editUrl = U('Wheel/editPrize');
        $this->delUrl = U('Wheel/delPrize');
        $this->sortUrl = U('Wheel/changeSort');
        $this->addUrl = "/index.php/Admin/Wheel/addPrize";
        $this->showUrl = U('Wheel/changeStatus');
        if(IS_AJAX){
            //查询参数
            $name = I('name','','trim');
            $cate = I('cate','','trim');
            $type = I('type','','trim');
            $status = I('status','','trim');

            //查询条件拼接
            if( $name !='' ) {
                $where['name'] = array('like','%'.$name.'%');
            }
            if( $cate !='' ) {
                $where['cate'] = array('eq',$cate);
            }

            if( $type !='' ) {
                $where['type'] = array('eq',$type);
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
            $db = M('Prize');
            $lists = $db->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();

            //数据处理
            foreach ($lists as $key => $val){
                $lists[$key]['cate'] = $val['cate'] == 1 ? '抽奖奖品' : '派奖奖品';
                if($val['cate']==1){
                    if($val['type'] == 1){
                        $lists[$key]['type'] = '特殊奖';
                    }else{
                        $lists[$key]['type'] = '金币('.$val['amount'].')';
                    }
                }else{
                    $lists[$key]['type'] = '——';
                }
                
                $lists[$key]['addTime'] = date('Y-m-d H:i:s',$val['addTime']);
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
     * [config 抽奖配置]
     * @return [type] [description]
     */
    public function config(){
        $conf = M('Config')->where(['flag'=>2])->select();
        if(IS_POST){
            $data['score'] = I('post.score',10,'intval');
            $data['rule'] = I('post.rule','');
            $data['status'] = I('post.status',1,'intval');
            $type = ['number','textarea','number'];
            if(trim($data['status']) == ''){
                $this->wrong('请配置抽奖规则');
            }
            M()->startTrans();
            if($conf){
                foreach($conf as $val){
                    $temp[$val['config']] = $val['value'];
                }
                $i = 0;
                foreach ($data as $k=>$v){
                    $newArr = array('config'=>$k,'value'=>trim($v),'type'=>$type[$i],'flag'=>2);
                    if(!isset($temp[$k])){
                        if(!$addRes = M('config')->add($newArr)){
                            M()->rollback();
                            $this->wrong('保存失败');
                        }
                    }else{
                        if($v!=$temp[$k])
                            if(!$upRes = M('config')->where(['config'=>$k,'flag'=>2])->save($newArr)){
                                M()->rollback();
                                $this->wrong('保存失败');
                            }
                    }
                    $i++;
                }
            }else{
                $i = 0;
                foreach($data as $k=>$v){
                    $newArr[] = array('config'=>$k,'value'=>trim($v),'type'=>$type[$i],'flag'=>2);
                    $i++;
                }
                if(!$insRes = M('config')->insertAll($newArr)){
                    M()->rollback();
                    $this->wrong('保存失败');
                }
            }
            //保存奖品信息
            $prize = I('prize','','trim');
            $level = I('level','','trim');
            $chance = I('chance','','trim');
            $prizeArr = array();
            foreach ($prize as $key => $value) {
                if($value && $level[$key]!='' && $chance[$key]!=''){
                    $OnePrize = array();
                    $OnePrize['pid'] = $value;
                    $OnePrize['chance'] = $chance[$key];
                    $OnePrize['level'] = $level[$key];
                    $prizeArr[] = $OnePrize;
                }
            }
            if(!$res = D('WheelPrize')->update($prizeArr)){
                M()->rollback();
                $this->wrong('保存失败');
            }
            M()->commit();
            $this->ok('保存成功');
        }else{
            if($conf){
                $info = [];
                foreach ($conf as $key => $value) {
                    $info[$value['config']] = $value['value'];
                }
                //抽奖配置的奖品信息
                $aprize = M('WheelPrize')->select();
                $this->aprize = $aprize;
                $this->info = $info;
            }
            $award = M('Prize')->where(['cate'=>1,'status'=>1])->order('sorts asc,id desc')->getField('id,name');
            $this->award = $award;
            $this->display();
        }
    }

	/**
	 * [addPrize 添加、编辑奖品信息]
	 */
	public function addPrize(){
        if(IS_POST){
            //接收数据
            $addResult = D('Prize')->update();
            if( !$addResult ) {
                $this->wrong(D('Prize')->getError());
            } else {
                $this->ok('保存成功');
            }
        }else{
            $id = I('id','0','int');
            if($id){
                $model = M('Prize');
                $info = $model->where(array('id'=>$id))->find();
                if( empty($info) ){
                    $this->error('数据不存在');
                }
                $this->assign('info',$info);
            }
            $this->display();
        }
	}

    /**
     * 修改奖品状态
     */
    public function changeStatus(){
        $res = D('Prize')->changeStatus();
        if( !$res ) {
            $this->wrong(D('Prize')->getError());
        } else {
            $this->ok('操作成功');
        }
    }

    /**
     * 修改排序
     */
    public function changeSort(){
        $res = D('Prize')->changeSort();
        if( !$res ) {
            $this->wrong(D('Prize')->getError());
        } else {
            $this->ok('操作成功');
        }
    }

    /**
     * 双击编辑信息
     */
    public function editPrize(){
        $id = I('id',0,'intval');
        $data['name'] = I('name','','trim');
        $rec = M('Prize')->where(['name'=>$data['name']])->find();
        if($rec && $rec['id']!=$id){
        	$this->wrong('该名称已存在');
        }
        $data['updateTime'] = time();
        $editRec = M('Prize')->where(['id'=>$id])->save($data);
        if(!$editRec){
        	$this->wrong('操作失败');
        }
        $this->ok('操作成功');
    }
    /**
     * 删除奖品信息
     */
    public function delPrize(){
        $id = I('id',0,'intval');
        $rec = M('Prize')->where(['id'=>$id])->find();
        if(empty($rec)){
            $this->wrong('信息不存在');
        }
        $rec = M('WheelPrize')->where(['pid'=>$id])->find();
        $rec1 = M('WheelRecord')->where(['pid'=>$id])->find();
        if($rec){
            $this->wrong('操作失败，该奖品正在参与抽奖活动');
        }
        if($rec1){
            $this->wrong('操作失败，该奖品存在中奖记录');
        }

        $res = M('Prize')->delete($id);
        if(!$res){
            $this->wrong('操作失败');
        }
        $this->ok('操作成功');
    }

    /**
     * [record 中奖列表]
     * @return [type] [description]
     */
    public function record(){
        $this->dataUrl = U('Wheel/record');
        $this->delUrl = U('Wheel/delRecord');
        $this->dealUrl = '/index.php/Admin/Wheel/editRecord';
        if(IS_AJAX){
            //查询参数
            $name = I('name','','trim');
            $nickname = I('nickname','','trim');
            $type = I('type','','trim');
            $isDeal = I('isDeal','','trim');

            //查询条件拼接
            if( $name !='' ) {
                $where['name'] = array('like','%'.$name.'%');
            }
            if( $nickname !='' ) {
                $where['nickname'] = array('like','%'.$nickname.'%');
            }

            if( $type !='' ) {
                $where['type'] = array('eq',$type);
            }
            if( $isDeal !='' ) {
                $where['isDeal'] = array('eq',$isDeal);
            }
            $where['fid'] = 0;

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
            $db = D('WheelRecordView');
            $lists = $db->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();

            //数据处理
            foreach ($lists as $key => $val){
                $lists[$key]['nickname'] = '[编号:'.$val['number'].']'.$val['nickname'];
                if($val['type'] == 1){
                    $lists[$key]['type'] = '特殊奖';
                }else{
                    $lists[$key]['type'] = '金币('.$val['amount'].'个)';
                }
                $lists[$key]['createTime'] = date('Y-m-d H:i:s',$val['createTime']);
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
     * [editRecord 编辑派奖信息]
     * @return [type] [description]
     */
    public function editRecord(){
        if(IS_POST){
            $fid = I('fid',0,'intval');
            if(!$fid){
                $this->wrong('参数错误');
            }
            $users = I('user/a');
            $awards = I('award/a');
            $prizes = M('prize')->where(['cate'=>2])->getField('id,name');
            foreach ($users as $key => $val) {
                if(!$awards[$key]){
                    $this->wrong('派奖信息不完整');
                    exit;
                }
                $dataList[] = ['uid'=>$val,'pid'=>$awards[$key],'name'=>$prizes[$awards[$key]],'fid'=>$fid,'isDeal'=>1,'createTime'=>time()];
            }
            M()->startTrans();
            $res = M('WheelRecord')->addAll($dataList);
            if(!$res){
                M()->rollback();
                $this->wrong('操作失败');
            }
            $saveRes = M('WheelRecord')->where(['id'=>$fid])->save(['isDeal'=>1]);
            if($saveRes === false){
                M()->rollback();
                $this->wrong('操作失败');
            }
            //生成中奖公告信息
            $nres = $this->addNotice($fid,$dataList);
            if(!$nres){
                M()->rollback();
                $this->wrong('操作失败');
            }
            M()->commit();
            $this->ok('操作成功');
        }else{
            $id = I('id',0,'intval');
            $info = D('WheelRecordView')->where(['id'=>$id])->find();
            $children = D('WheelRecordView')->where(['fid'=>$id])->order('id asc')->select();
            $parents = [];
            if(empty($children)){
                $parents = D('User')->getParents($info['uid']);
                $prize = M('prize')->where(['status'=>1,'cate'=>2])->getField('id,name');
            }else{
                $prize = M('prize')->where(['cate'=>2])->getField('id,name');
            }
            $this->prize = $prize;
            $this->parents = $parents;
            $this->children = $children;
            $this->info = $info;
            $this->fid = $id;
            $this->display();
        }
    }

    /**
     * [delRecord 删除中奖记录]
     * @return [type] [description]
     */
    public function delRecord(){
        $id = I('id',0,'intval');
        if(!$id){
            $this->wrong('参数错误');
        }
        $info = M('WheelRecord')->where(['id'=>$id])->find();
        if(empty($info)){
            $this->wrong('记录不存在');
        }
        $res = M('WheelRecord')->where("id = $id or fid = $id")->delete();
        if(!$res){
            $this->wrong('操作失败');
        }
        $this->ok('操作成功');
    }

    /**
     * [notices 中奖公告列表]
     * @return [type] [description]
     */
    public function notices(){
        $this->dataUrl = U('Wheel/notices');
        $this->delUrl = U('Wheel/delNotice');
        $this->editUrl = U('Wheel/editRow');
        $this->showUrl = U('Wheel/updateStatus');
        $this->addUrl = '/index.php/Admin/Wheel/editNotice';
        if(IS_AJAX){
            //查询参数
            $title = I('title','','trim');
            $name = I('name','','trim');

            //查询条件拼接
            if( $title !='' ) {
                $where['title'] = array('like','%'.$title.'%');
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
            $db = M('WheelNotice');
            $lists = $db->alias('n')->field('n.*,nickname')
                    ->join('wg_user on wg_user.id = n.uid')
                    ->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();

            //数据处理
            foreach ($lists as $key => $val){
                $lists[$key]['createTime'] = date('Y-m-d H:i:s',$val['createTime']);
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
     * [editNotice 编辑公告内容]
     * @return [type] [description]
     */
    public function editNotice(){
        if(IS_POST){
            if(D('WheelNotice')->update()){
                $this->ok('操作成功');
            }else{
                $this->wrong(D('WheelNotice')->getError());
            }
        }else{
            $id = I('id',0,'intval');
            if($id){
                $info = M('WheelNotice')->where(['id'=>$id])->find();
                $this->info = $info;
            }
            $this->display();
        }
    }
    /**
     * 双击编辑信息
     */
    public function editRow(){
        $id = I('id',0,'intval');
        $data['title'] = I('title','','trim');
        
        $data['updateTime'] = time();
        $editRec = M('WheelNotice')->where(['id'=>$id])->save($data);
        if(!$editRec){
            $this->wrong('操作失败');
        }
        $this->ok('操作成功');
    }
    /**
     * [updateStatus 编辑中奖公告状态]
     * @return [type] [description]
     */
    public function updateStatus(){
        $res = D('WheelNotice')->changeStatus();
        if( !$res ) {
            $this->wrong(D('WheelNotice')->getError());
        } else {
            $this->ok('操作成功');
        }
    }

    /**
     * [delNotice 删除消息公告]
     * @return [type] [description]
     */
    public function delNotice(){
        $res = D('WheelNotice')->delRec();
        if( !$res ) {
            $this->wrong(D('WheelNotice')->getError());
        } else {
            $this->ok('操作成功');
        }
    }

    /**
     * [addNotice 根据派奖信息生成中奖公告]
     * @param [type] $rid [中奖记录id]
     * @param [type] $arr [派奖信息]
     */
    protected function addNotice($rid,$arr){
        $info = M('WheelRecord')->where(['rid'=>$rid])->find();
        $uinfo = M('User')->where(['id'=>$info['uid']])->field('number,nickname')->find();
        $title = 'ID:'.$uinfo['number'].' / '.$uinfo['nickname'];
        $content = '';
        $content .= '<p>恭喜'.$title.'抽中'.$info['name'].'！中奖详情如下：</p>';
        $uids = array_column($arr,'uid');
        if($uids){
            $userInfo = M('User')->where(['id'=>['in',$uids]])->getField('id,number,nickname');
            foreach ($arr as $key => $value) {
                $content .= '<p>ID:'.$userInfo[$value['uid']]['number'].' / '.$userInfo[$value['uid']]['nickname'].'&nbsp;&nbsp;奖品:'.$value['name'].'</p>';
            }
            $data = [
                'uid'=>$info['uid'],
                'rid'=>$rid,
                'title'=>$title,
                'content'=>$content,
                'createTime'=>time(),
                'updateTime'=>time(),
            ];
            return M('WheelNotice')->add($data);
        }
        return true;
    }
}