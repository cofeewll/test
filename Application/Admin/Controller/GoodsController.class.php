<?php
namespace Admin\Controller;
use Common\Util\TreeUtil;

/**
* 商品控制器
*/
class GoodsController extends BaseController
{
	
	public $privilege = ['cate'=>['addCate','editCate','delCate','changeSort','changeStatus'],
                        'index'=>['sortGoods','addGoods','changeNew','batchOpt','ajaxGetSpecSelect','ajaxGetSpecInput'],
                        ];

    /**
     * [index 商品列表]
     * @return [type] [description]
     */
    public function index(){
        $this->dataUrl = U('Goods/index');
        $this->sortUrl = U('Goods/sortGoods');
        $this->addUrl = "/index.php/Admin/Goods/addGoods";
        $this->optUrl = U('Goods/batchOpt');
        $this->newUrl = U('Goods/changeNew');
        if(IS_AJAX){
            //查询参数
            $name = I('name','','trim');
            $shopId = I('shopId','','intval');
            $cid = I('cid','','intval');
            $status = I('status','','trim');

            //查询条件拼接
            if( $name !='' ) {
                $where['g.name'] = array('like','%'.$name.'%');
            }
            if( $shopId !='' ) {
                $where['shopId'] = array('eq',$shopId);
            }
            if( $status !='' ) {
                if($status == 1){
                    $where['g.status'] = array('egt',0);
                }else{
                    $where['g.status'] = array('eq',$status);
                }
            }
            if( $cid !='' ) {
                $cids = M('GoodsCate')->where(['fid'=>$cid])->getField('id',true);
                if($cids){
                    $cids = implode(',', $cids);
                    $where['_string'] = "cid = $cid or cid in ($cids)";
                }else{
                    $where['cid'] = array('eq',$cid);
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
            $db = M('Goods');
            $lists = $db->alias('g')->field('g.*,s.title,c.name as cname')->where($where)
                    ->join('wg_shop as s on g.shopId = s.id','left')
                    ->join('wg_goods_cate as c on g.cid = c.id','left')
                    ->limit($start,$length)
                    ->order($order)
                    ->select();
            foreach ($lists as $k => &$v) {
                $v['cid'] = $v['cname'];
            }

            //数据返回
            $totalCount = $db->alias('g')->where($where)->count();
            $totalPage = ceil($totalCount/$length);
            $result['page'] = $page;
            $result['total'] = $totalPage;
            $result['records'] = $totalCount;
            $result['rows'] = $lists;

            $this->ajaxReturn($result);
        } else {
            $cates = M('GoodsCate')->field('id,fid,name')->select();
            $cates = TreeUtil::listToTreeOne( $cates ,  0 , '|— ' , 'id' , 'fid' , 'html');

            foreach ($cates as $key => $v){
                $cates[$key]['name'] = $v['html'].$v['name'];
            }
            $shops = M('Shop')->where(['status'=>['gt','0']])->field('id,title')->select();
            $this->shops =$shops;
            $this->cates = $cates;
            $this->display();
        }
    }

    /**
     * [addGoods 添加、编辑商品]
     */
    public function addGoods(){
        if(IS_POST){
            $goods_id = I('id',0,'intval');
            M()->startTrans();
            $addRes = D('Goods')->update();
            if(!$goods_id){
                $goods_id = $addRes;
            }
            if(!$addRes){
                M()->rollback();
                $this->wrong(D('Goods')->getError());
            }else{
                if(!D('Goods')->afterSave($goods_id)){
                    M()->rollback();
                    $this->wrong('操作失败');
                }
                M()->commit();
                $this->ok('操作成功');
            }
        }else{
            $id = I('id',0,'intval');
            $provinces = [];
            if($id){
                $info = M('Goods')->where(['id'=>$id])->find();
                $info['detail'] = htmlspecialchars_decode($info['detail']);
                $info['fid'] = M('GoodsCate')->where(['id'=>$info['cid']])->getField('fid');
                $info['scates'] = getCates($info['fid']);
                $images = M('Picture')->where(['sid'=>$id,'type'=>1])->getField('path',true);
                $info['images'] = $images?implode(',', $images):'';
                //商家信息
                $info['sname'] = M('Shop')->where(['id'=>$info['shopId']])->getField('title');
                $this->info = $info;
                $provin = M('Shop')->where(['id'=>$info['shopId']])->getField('provinces');
                $proTemp = [];
                if($provin){
                    $proTemp = explode('|', $provin);
                }
                $ship = M('GoodsShip')->where(['goodsId'=>$id])->getField('province,fee');
                if($proTemp){
                    if($ship) $temp = array_keys($ship);
                    foreach ($proTemp as $k => $v) {
                        $provinces[$k]['province'] = $v;
                        if($temp && in_array($v, $temp)){
                            $provinces[$k]['fee'] = $ship[$v];
                        }
                    }
                }
            }
            //商品分类
            $cates = getCates();
            //商品模型
            $types = M('GoodsType')->where(['status'=>1])->select();
            $this->types = $types;
            $this->cates = $cates;
            //特殊地区
            $this->provinces = $provinces;
            $this->display();
        }
    }

    /**
     * [batchOpt 批量操作]
     * @return [type] [description]
     */
    public function batchOpt(){
        $goods_id = I('post.goods_id');
        $opt = I('post.opt','','trim');
        $text = I('post.text','','trim');
        if(is_array($goods_id)&& empty($goods_id) || is_string($goods_id)&& trim($goods_id)==''){
            $this->wrong('请选择要操作的商品');
        }
        $where = ['id'=>['in',$goods_id]];
        switch ($opt) {
            case 'new':
                $data['isNew'] = 1;
                break;
            case 'hot':
                $data['isHot'] = 1;
                break;
            case 'wait':
                $data['status'] = -1;
                break;
            case 'access':
                $data['status'] = 0;
                break;
            case 'refuse':
                $data['status'] = -2;
                if($text == ''){
                    $this->wrong('请输入操作备注');
                }
                break;
            case 'takeoff':
                $data['status'] = -3;
                if($text == ''){
                    $this->wrong('请输入操作备注');
                }
                break;
            default:
                $this->wrong('操作错误');
                break;
        }
        //修改商品状态时生成商家消息-access/refuse/takeoff
        $this->sendNotice($goods_id,$opt,$text);
        M('Goods')->where($where)->save($data);
        
        $this->ok('操作成功');
    }

    /**
     * 动态获取商品规格选择框 根据不同的数据返回不同的选择框
     */
    public function ajaxGetSpecSelect(){
        $goods_id = I('get.goods_id/d') ? I('get.goods_id/d') : 0;        
        
        $specList = M('Spec')->where("typeId = ".I('get.spec_type/d'))->order('`sorts` asc')->select();
        foreach($specList as $k => $v)        
            $specList[$k]['spec_item'] = M('SpecItem')->where("specId = ".$v['id'])->order('id')->getField('id,item'); // 获取规格项                
        $items_ids = [];
        if($goods_id){
            $items_id = M('GoodsSpec')->where('goodsId = '.$goods_id)->getField("GROUP_CONCAT(`key` SEPARATOR '_') AS items_id");
            $items_ids = explode('_', $items_id);
        }
        $this->assign('items_ids',$items_ids); 
        // // 获取商品规格图片                
        // if($goods_id)
        // {
        //    $specImageList = M('SpecImage')->where("goods_id = $goods_id")->getField('spec_image_id,src');                 
        // }        
        // $this->assign('specImageList',$specImageList);
        
        $this->assign('specList',$specList);
        exit($this->fetch('ajax_spec_select')) ;        
    }   
    /**
     * 动态获取商品规格输入框 根据不同的数据返回不同的输入框
     */    
    public function ajaxGetSpecInput(){    
         $goods_id = I('get.goods_id/d') ? I('get.goods_id/d') : 0;
         $str = D('Goods')->getSpecInput($goods_id ,I('post.spec_arr/a',[[]]));
         exit($str);   
    }

    /**
     * [cate 商品分类列表]
     * @return [type] [description]
     */
	public function cate(){
		$this->dataUrl = U('Goods/cate');
        $this->editUrl = U('Goods/editCate');
        $this->delUrl = U('Goods/delCate');
        $this->sortUrl = U('Goods/changeSort');
        $this->addUrl = "/index.php/Admin/Goods/addCate";
        $this->showUrl = U('Goods/changeStatus');
        if(IS_AJAX){
            //查询参数
            $fid = I('fid','','trim');
            $status = I('status','','trim');

            //查询条件拼接
            
            if( $status !='' ) {
                $where['status'] = array('eq',$status);
            }
            if( $fid !='' ) {
                $where['_string'] = "id = $fid or fid = $fid";
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
            $db = M('GoodsCate');
            $lists = $db->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();
            //格式化处理
            $lists = TreeUtil::listToTreeOne( $lists ,  0 , '|— ' , 'id' , 'fid' , 'html');
            foreach ($lists as $key => $v){
                $lists[$key]['name'] = $v['html'].$v['name'];
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
            $cates = M('GoodsCate')->where(['fid'=>0])->getField('id,name');
            $this->cates = $cates;
            $this->display();
        }
	}

	/**
	 * [addCate 添加、编辑分类信息]
	 */
	public function addCate(){
		if(IS_POST){
			//接收数据
            $addResult = D('GoodsCate')->update();
            if( !$addResult ) {
                $this->wrong(D('GoodsCate')->getError());
            } else {
                $this->ok('保存成功');
            }
		}else{
			$id = I('id',0,'intval');
			if($id>0){
				$info = M('GoodsCate')->where(array('id'=>$id))->find();
				$this->info = $info;
			}

	        $cates = M('GoodsCate')->where(array('status'=>1,'fid'=>0))->order('sorts asc')->select();
	        
	        $this->assign('cates', $cates);

			$this->display();
		}
	}

    
    /**
     * 修改分类状态
     */
    public function changeStatus(){
        $res = D('GoodsCate')->changeStatus();
        if( !$res ) {
            $this->wrong(D('GoodsCate')->getError());
        } else {
            $this->ok('操作成功');
        }
    }

    /**
     * 修改排序
     */
    public function changeSort(){
        $res = D('GoodsCate')->changeSort();
        if( !$res ) {
            $this->wrong(D('GoodsCate')->getError());
        } else {
            $this->ok('操作成功');
        }
    }

    /**
     * 修改商品排序
     */
    public function sortGoods(){
        $id = intval($_POST['id']);
        $value = intval($_POST['value']);
        if(!$id){
            $this->wrong('缺失参数');
        }
        if (!is_numeric($value)) {
            $this->wrong('更新失败，排序只能填写数字');
        }
        $data = array();
        $data['id'] = $id;
        $data['sort'] = $value;
        $optRes = M('Goods')->save($data);
        if($optRes === false){
            $this->wrong('操作失败');
        }
        $this->ok('操作成功');
    }

    /**
     * [changeNew 商品是新品/热销]
     * @return [type] [description]
     */
    public function changeNew(){
        $id = intval($_POST['id']);
        $value = intval($_POST['value']);
        $type = trim($_POST['type']);
        if(!$id){
            $this->wrong('缺失参数');
        }
        if($value == 0){
            if($type == 'new')
                $data = array( 'isNew'=>1 );
            elseif($type == 'hot')
                $data = array( 'isHot'=>1 );
        }elseif($value == 1){
            if($type == 'new')
                $data = array( 'isNew'=>0 );
            elseif($type == 'hot')
                $data = array( 'isHot'=>0 );
        }else{
            $this->wrong('参数不符合规范');
        }
        $data['id'] = $id;
        $optRes = M('Goods')->save($data);
        if($optRes === false){
            $this->wrong('操作失败');
        }
        $this->ok('操作成功');
    }

    
    /**
     * 删除分类信息
     */
    public function delCate(){
        $id = I('id',0,'intval');
        $rec = M('GoodsCate')->where(['id'=>$id])->find();
        if(empty($rec)){
            $this->wrong('信息不存在');
        }
        $count = M('GoodsCate')->where("fid = {$id}")->count("id");
        if($count > 0 ){
            $this->wrong('操作失败，该分类存在子分类!');
        }
        $rec = M('Goods')->where(['cid'=>$id])->find();
        
        if($rec){
            $this->wrong('操作失败，该分类下存在商品');
        }
        $res = M('GoodsCate')->delete($id);
        if(!$res){
            $this->wrong('操作失败');
        }
        $this->ok('操作成功');
    }

    /**
     * [sendNotice 批量操作、商品审核和强制下架生成消息]
     * @param  [type] $goods_id [商品id]
     * @param  [type] $opt      [操作]
     * @param  [type] $text     [操作说明]
     * @return [type]           [description]
     */
    public function sendNotice($goods_id,$opt,$text){
        if(!in_array($opt, ['access','refuse','takeoff'])) return false;
        $where = ['id'=>['in',$goods_id]];
        $goods = M('Goods')->where($where)->field('name,shopId,status')->select();
        foreach ($goods as $k => $good) {
            switch ($opt) {
                case 'access':
                    if( $good['status']!= 0 ){
                        $title = '商品审核结果通知';
                        $content = '商品：'.$good['name'].'审核已通过，您可以对其进行上架操作开始销售。';
                        addNotice($title,$content,2,$good['shopId']);
                    }
                    $data['status'] = 0;
                    break;
                case 'refuse':
                    if( $good['status']!= -2 ){
                        $title = '商品审核结果通知';
                        $content = '商品：'.$good['name'].'审核被拒绝，管理员操作说明：'.$text;
                        addNotice($title,$content,2,$good['shopId']);
                    }
                    break;
                case 'takeoff':
                    if( $good['status']!= -3 ){
                        $title = '商品强制下架通知';
                        $content = '商品：'.$good['name'].'已被管理员强制下架，管理员操作说明：'.$text;
                        addNotice($title,$content,2,$good['shopId']);
                    }
                    break;
            }
        }
    }
}