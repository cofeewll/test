<?php
namespace Admin\Controller;
 
use Common\Util\TreeUtil;

/**
 * 权限管理
 * @author xiegaolei
 *
 */
class ManagerController extends BaseController {

    // 控制器权限分块
    public $privilege=array(
            'adminList' => array('adminAdd','adminAddSave','adminDel','adminStatus','unLock'),
            'rolesList'   => array('rolesAdd', 'rolesAddSave','rolesDel','rolesStatus'),
            'rulesList'   => array('rulesAdd', 'rulesSave', 'rulesDel','rulesSort','rulesStatus'),
    );

    /**
     * 管理员列表
     */
    public function adminList(){
        $this->dataUrl = U('Manager/adminList');
        $this->addUrl = "/index.php/Admin/Manager/adminAdd";
        $this->delUrl = U('Manager/adminDel');
        $this->showUrl = U('Manager/adminStatus');
        if(IS_AJAX){
            //查询参数
            $username = I('username','','trim');
            $mobile = I('mobile','','trim');
            $type = I('type','','trim');

            //查询条件拼接
            if( $username !='' ) {
                $where['username'] = array('like','%'.$username.'%');
            }

            if( $mobile !='' ) {
                $where['mobile'] = array('like','%'.$mobile.'%');
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
            $sortRow = I('sidx','id','trim');      //排序列
            $sort = I('sord','asc','trim');        //排序方式
            $order = $sortRow.' '.$sort;

            //数据查询
            $db = D('AdminView');
            $lists = $db->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();

            //数据处理
            foreach ($lists as $key => $val){
                $lists[$key]['loginTime'] = empty($val['loginTime']) ? '':date('Y-m-d H:i:s',$val['loginTime']);
                $lists[$key]['loginIp'] = $val['loginIp']?long2ip($val['loginIp']):'';
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
            $this->type = M('auth_group')->where(true)->select();
            $this->display();
        }
    }
      
    
    /**
     * 添加管理员
     */
    public function adminAdd(){
        $id = I('id','0','int');
        if($id){
            $admin = M('admin');
            $info = $admin->where(array('id'=>$id))->find();
            if( empty($info) ){
                $this->error('数据不存在');
            }
            $groupId = M('auth_group_access')->where(array('uid'=>$id))->getfield('group_id');
            $info['type'] = $groupId;
            $this->assign('info',$info);
        }
        $groups = M('auth_group')->order('id asc')->select();
        $this->assign('groups', $groups);
        
        $this->display();
    }
    
    
    /**
     * 保存管理员
     */
    public function adminAddSave(){
        //接收数据
        $id = I('id',0,'intval');
        $group = I('group','','trim');
        if(empty($group)){
            $this->wrong('请选择角色！');
        }
        //事务开始
        M()->startTrans();
        $addResult = D('Admin')->update();
        if($addResult){
            $id = $id?$id:$addResult;
            if(M('auth_group_access')->where(array('uid'=>$id))->find()){
                $res = M('auth_group_access')->where(array('uid'=>$id))->setfield('group_id',$group);
                if($res===false){
                    M()->rollback();
                    $this->wrong('保存失败');
                }
            }else{
                $res = M('auth_group_access')->add(array(
                    'uid' =>$id,
                    'group_id' => $group
                    ));
                if(!$res){
                    M()->rollback();
                    $this->wrong('保存失败');
                }
            }
            
        }else{
            M()->rollback();
            $this->wrong(D('Admin')->getError());
        }
        M()->commit();
        $this->ok('保存成功');
    }

    
    /**
     * 删除
     */
    public function adminDel(){
        $id = I('id','0','int');
        if ($id == 0 || $id == 1) {
            $this->wrong('参数有误');
        }
             
        $delResult = M('admin')->where(array('id'=>$id))->delete();

        if( $delResult > 0 ) {
            M('auth_group_access')->where(array('uid'=>$id))->delete();
            $this->ok('删除成功');
        } else {
            $this->wrong('删除失败');
        }
    }
    
    /**
     * 角色列表
     */
    public function rolesList(){
        $this->dataUrl = U('Manager/rolesList');
        $this->addUrl = "/index.php/Admin/Manager/rolesAdd";
        $this->delUrl = U('Manager/rolesDel');
        $this->showUrl = U('Manager/rolesStatus');
        if(IS_AJAX){
            //分页参数
            $length = I('rows',10,'intval');   //每页条数
            $page = I('page',1,'intval');      //第几页
            $start = ($page - 1) * $length;     //分页开始位置

            //排序
            $sortRow = I('sidx','id','trim');      //排序列
            $sort = I('sord','asc','trim');        //排序方式
            $order = $sortRow.' '.$sort;

            //数据查询
            $db = M('auth_group');
            $lists = $db->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();

            //数据处理
            // foreach ($lists as $key => $val){
                
            // } 

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
     * 添加编辑角色
     */
    public function rolesAdd(){
        $rules = M('auth_rule')->where(array('status'=>1))->order('sorts asc')->select();
        //格式化成树形
        $rules = TreeUtil::listToTreeMulti($rules, 0, 'id', 'pid', 'child');
        
        $this->assign('rules', $rules);
        $id = I('id','0','int');
        if($id){
            // if($id == 1){
            //     $url = $_SERVER['HTTP_REFERER'];
            //     header("Location:$url");exit;
            // }
             
            $roles = M('auth_group');
            $info = $roles->where(array('id'=>$id))->find();
            $info['rules'] = explode(',', $info['rules']);
            $this->assign('info',$info);
        }
        
        $this->display();
    }
    
    
    /**
     * 保存角色
     */
    public function rolesAddSave(){
         
        //接收数据
        $addResult = D('AuthGroup')->update();

        if( !$addResult ) {
            $this->wrong(D('AuthGroup')->getError());
        } else {
            $this->ok('保存成功');
        }
    }

    
    /**
     * 删除
     */
    public function rolesDel(){
        $id = I('id','0','int');
        if ($id == 0 || $id == 1) {
            $this->wrong('参数有误');
        }

        $checkResult = M('auth_group_access')->where(array('group_id'=>$id))->getfield('uid');

        if( $checkResult > 0 ){
            $this->wrong('删除失败，该角色下有用户');
        }
             
        $delResult = M('auth_group')->where(array('id'=>$id))->delete();

        if( $delResult > 0 ) {
            $this->ok('删除成功');
        } else {
            $this->wrong('删除失败');
        }
    }
    
     
    
    /**
     * 权限列表
     */
    public function rulesList(){
        $this->dataUrl = U('Manager/rulesList');
        $this->addUrl = "/index.php/Admin/Manager/rulesAdd";
        $this->sortUrl = U('Manager/rulesSort');
        $this->delUrl = U('Manager/rulesDel');
        $this->showUrl = U('Manager/rulesStatus');
        if (IS_AJAX){
            //分页参数
            $length = I('rows',10,'intval');   //每页条数
            $page = I('page',1,'intval');      //第几页
            $start = ($page - 1) * $length;     //分页开始位置

            //排序
            $sortRow = I('sidx','id','trim');      //排序列
            $sort = I('sord','asc','trim');        //排序方式
            $order = $sortRow.' '.$sort;

            //数据查询
            $db = M('auth_rule');
            $lists = $db->order('sorts')->select();
            
            //格式化处理
            $lists = TreeUtil::listToTreeOne( $lists ,  0 , '|— ' , 'id' , 'pid' , 'html');

            foreach ($lists as $key => $v){
                $lists[$key]['title'] = $v['html'].$v['title'];
            }
            //数据返回
            $totalCount = $db->where($where)->count();
            $totalPage = ceil($totalCount/$length);
            $result['page'] = $page;
            $result['total'] = $totalPage;
            $result['records'] = $totalCount;
            $result['rows'] = $lists;
             
            $this->ajaxReturn($result);
             
        }else{
            $this->display();
        }
    }
    
    /**
     * 添加权限
     */
    public function rulesAdd(){
        $id = I('id','0','int');
        if ($id){
            $info = M('auth_rule')->where(array('id'=>$id))->find();
            $this->assign('info',$info);
        }
    
        $rules = M('auth_rule')->where(array('status'=>1))->order('sorts asc')->select();
        //格式化处理
        $rules = TreeUtil::listToTreeOne( $rules ,  0 , '|— ' , 'id' , 'pid' , 'html');
         
        $this->assign('rules', $rules);

        $this->display();
    }
    
    /**
     * 保存权限
     */
    public function rulesSave(){
        //接收数据
        $addResult = D('AuthRule')->update();

        if( !$addResult ) {
            $this->wrong(D('AuthRule')->getError());
        } else {
            $this->ok('保存成功');
        }
    }
    
    
    /**
     * [sortColum 栏目排序]
     * @return [type] [description]
     */
    public function rulesSort(){
        $addResult = D('AuthRule')->changeSort();

        if( !$addResult ) {
            $this->wrong(D('AuthRule')->getError());
        } else {
            $this->ok('编辑成功');
        }
    }
    
    
    
    /**
     * 删除
     */
    public function rulesDel(){
        $id = I('id','0','int');
        if ($id == 0) {
            $this->wrong('参数有误');
        }

        $checkResult = M('auth_rule')->where(array('pid'=>$id))->getfield('id');

        if( $checkResult > 0 ) {
            $this->wrong('删除失败，该菜单下有子菜单');
        }
             
        $delResult = M('auth_rule')->where(array('id'=>$id))->delete();

        if( $delResult > 0 ) {
            $this->ok('删除成功');
        } else {
            $this->wrong('删除失败');
        }
    }
    
    public function adminStatus(){
        $id = I('post.id',0,'intval');
        if($id==1){
            $this->wrong('没有权限');
        }
        $addResult = D('Admin')->changeStatus();

        if( !$addResult ) {
            $this->wrong(D('Admin')->getError());
        } else {
            $this->ok('编辑成功');
        }
    }

    public function rolesStatus(){
        $id = I('post.id',0,'intval');
        if($id==1){
            $this->wrong('没有权限');
        }
        $addResult = D('AuthGroup')->changeStatus();

        if( !$addResult ) {
            $this->wrong(D('AuthGroup')->getError());
        } else {
            $this->ok('编辑成功');
        }
    }
    public function rulesStatus(){
        $id = I('post.id',0,'intval');
        if($id==1){
            $this->wrong('没有权限');
        }
        $addResult = D('AuthRule')->changeStatus();

        if( !$addResult ) {
            $this->wrong(D('AuthRule')->getError());
        } else {
            $this->ok('编辑成功');
        }
    }

    /**
     * [unLock 解锁管理员]
     * @return [type] [description]
     */
    public function unLock(){
        $roleId = $_SESSION['admin_auth']['roleId'];
        if($roleId != 1){
            $this->wrong('没有操作权限');
        }
        $id = I('post.id',0,'intval');
        $rec = M('admin')->where(['id'=>$id])->find();
        if(empty($rec)){
            $this->wrong('信息不存在');
        }
        if($rec['isAdmin'] == 0){
            //普通管理员解锁：修改密码、状态、错误次数
            $data = array(
                'password'=> '',
                'status'=> 1,
                'wrongNum' =>0,
                'updateTime'=>time(),
            );
            $res = M('admin')->where(['id'=>$id])->save($data);
            if($res === false){
                $this->ok('解锁失败');
            }else{
                $this->ok('解锁成功');
            }
        }else{
            $this->wrong('数据有误');
        }
    }
}
