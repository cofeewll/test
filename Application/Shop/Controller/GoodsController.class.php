<?php
namespace Shop\Controller;
use Common\Util\TreeUtil;

/**
* 商品控制器
*/
class GoodsController extends BaseController
{
	
	/**
	 * [index 商品列表]
	 * @return [type] [description]
	 */
	public function index(){
		$this->dataUrl = U('Goods/index');
        $this->editUrl = U('Goods/editGoods');
        $this->delUrl = U('Goods/delGoods');
        $this->addUrl = "/index.php/Shop/Goods/addGoods";
        $this->showUrl = U('Goods/changeOn');
        $this->newUrl = U('Goods/changeShow');
        if(IS_AJAX){
            //查询参数
            $name = I('name','','trim');
            $cid = I('cid','','intval');
            $status = I('status','','trim');

            //查询条件拼接
            if( $name !='' ) {
                $where['g.name'] = array('like','%'.$name.'%');
            }
            if( $cid !='' ) {
                $where['cid'] = array('eq',$cid);
            }
            if( $status !='' ) {
                $where['g.status'] = array('eq',$status);
            }

            $where['shopId'] = $this->sid;
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
            $lists = $db->alias('g')->field('g.*,c.name as cname')->where($where)
                    ->join('wg_goods_cate c on c.id = g.cid','left')
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
            $cates = [];
            $cateIds = M('Goods')->where(['shopId'=>$this->sid])->getField('cid',true);
            if($cateIds){
                $cates = M('GoodsCate')->where(['id'=>['in',$cateIds]])->field('id,name')->select();
            }
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
            $proTemp = [];
            $provin = M('Shop')->where(['id'=>$this->sid])->getField('provinces');
            if($provin){
                $proTemp = explode('|', $provin);
            }
            $provinces = [];
			if($id){
				$info = M('Goods')->where(['id'=>$id])->find();
                $info['detail'] = htmlspecialchars_decode($info['detail']);
                $info['fid'] = M('GoodsCate')->where(['id'=>$info['cid']])->getField('fid');
                $info['scates'] = getCates($info['fid']);
                $images = M('Picture')->where(['sid'=>$id,'type'=>1])->getField('path',true);
                $info['images'] = $images?implode(',', $images):'';
                //特殊地区运费
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
                $this->info = $info;
			}else{
                if($proTemp){
                    foreach ($proTemp as $k => $v) {
                        $provinces[$k]['province'] = $v;
                        $provinces[$k]['fee'] = '';
                    }
                }
            }
            //商品分类
			$cates = getCates();
            //商品模型
            $types = M('GoodsType')->where(['shopId'=>$this->sid,'status'=>1])->select();
            $this->types = $types;
			$this->cates = $cates;
            //特殊地区
            // trace($provinces);
            $this->provinces = $provinces;
			$this->display();
		}
	}

    /**
     * [editGoods 双击编辑商品信息]
     * @return [type] [description]
     */
    public function editGoods(){
        $id = I('id',0,'intval');
        $data['name'] = I('name','','trim');
        $data['price'] = I('price',0,'floatval');
        $data['sorts'] = I('sorts',0,'intval');
        $rec = M('Goods')->where(['name'=>$data['name']])->find();
        if($rec && $rec['id']!=$id){
            $this->wrong('该商品名称已存在');
        }
        $editRec = M('Goods')->where(['id'=>$id])->save($data);
        if(!$editRec){
            $this->wrong('操作失败');
        }
        $this->ok('操作成功');
    }

    /**
     * [delGoods 删除商品信息]
     * @return [type] [description]
     */
    public function delGoods(){
        $id = I('id',0,'intval');
        $rec = M('Goods')->where(['id'=>$id])->find();
        if(empty($rec)){
            $this->wrong('信息不存在');
        }
        $rec = M('OrderGoods')->where(['goodsId'=>$id])->find();
        
        if($rec){
            $this->wrong('操作失败，该商品有对应订单');
        }
        $res = M('Goods')->delete($id);
        M('cart')->where(['goodsId'=>$id])->delete();
        M("orderComment")->where('goodsId ='.$id)->delete();  //商品评论
        M("GoodsSpec")->where('goodsId ='.$id)->delete();  //商品规格  
        M("collect")->where('goodsId ='.$id.' and type=1')->delete();  //商品收藏     
        if(!$res){
            $this->wrong('操作失败');
        }

        $this->ok('操作成功');
    }

    /**
     * [changeStatus 商品上下架]
     * @return [type] [description]
     */
    public function changeOn(){
        $res = D('Goods')->changeStatus();
        if( !$res ) {
            $this->wrong(D('Goods')->getError());
        } else {
            $this->ok('操作成功');
        }
    }

    /**
     * [changeShow 商品是新品]
     * @return [type] [description]
     */
    public function changeShow(){
        $id = intval($_POST['id']);
        $value = intval($_POST['value']);
        if(!$id){
            $this->wrong('缺失参数');
        }
        if($value == 0){
            $data = array('isIndex'=>1);
        }elseif($value == 1){
            $data = array('isIndex'=>0);
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
	 * [type 商品模型]
	 * @return [type] [description]
	 */
	public function type(){
		$this->dataUrl = U('Goods/type');
        $this->editUrl = U('Goods/editType');
        $this->delUrl = U('Goods/delType');
        $this->addUrl = "/index.php/Shop/Goods/addType";
        $this->showUrl = U('Goods/changeStatus');
        $this->specUrl = "/index.php/Shop/Goods/spec";
        if(IS_AJAX){
            //查询参数
            $name = I('name','','trim');
            $status = I('status','','trim');

            //查询条件拼接
            if( $name !='' ) {
                $where['name'] = array('like','%'.$name.'%');
            }
            
            if( $status !='' ) {
                $where['status'] = array('eq',$status);
            }

            $where['shopId'] = $this->sid;
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
            $db = M('GoodsType');
            $lists = $db->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();

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
	 * [addType 添加、编辑商品模型]
	 */
	public function addType(){
		if(IS_POST){	
			//接收数据
            $addResult = D('GoodsType')->update();
            if( !$addResult ) {
                $this->wrong(D('GoodsType')->getError());
            } else {
                $this->ok('保存成功');
            }
		}else{
			$id = I('id',0,'intval');
			if($id){
				$info = M('GoodsType')->where(['id'=>$id])->find();
				$this->info = $info;
			}
			$this->display();
		}
	}

	/**
     * 双击编辑信息
     */
    public function editType(){
        $id = I('id',0,'intval');
        $data['name'] = I('name','','trim');
        $rec = M('GoodsType')->where(['name'=>$data['name']])->find();
        if($rec && $rec['id']!=$id){
        	$this->wrong('该名称已存在');
        }
        $editRec = M('GoodsType')->where(['id'=>$id])->save($data);
        if(!$editRec){
        	$this->wrong('操作失败');
        }
        $this->ok('操作成功');
    }

    /**
     * 修改模型状态
     */
    public function changeStatus(){
        $res = D('GoodsType')->changeStatus();
        if( !$res ) {
            $this->wrong(D('GoodsType')->getError());
        } else {
            $this->ok('操作成功');
        }
    }

    /**
     * 删除分类信息
     */
    public function delType(){
        $id = I('id',0,'intval');
        $rec = M('GoodsType')->where(['id'=>$id])->find();
        if(empty($rec)){
            $this->wrong('信息不存在');
        }
        $rec = M('Goods')->where('typeId',$id)->find();
        
        if($rec){
            $this->wrong('操作失败，该模型下存在商品');
        }
        $res = M('GoodsType')->delete($id);
        if(!$res){
            $this->wrong('操作失败');
        }
        $this->ok('操作成功');
    }

    /**
     * [spec 规格列表]
     * @return [type] [description]
     */
    public function spec(){
    	$tid = I('tid',0,'intval');
    	$this->tid = $tid;
    	$this->dataUrl = U('Goods/spec',['tid'=>$tid]);
        $this->editUrl = U('Goods/editSpec');
        $this->delUrl = U('Goods/delSpec');
        $this->addUrl = "/index.php/Shop/Goods/addSpec";
        $this->sortUrl = U('Goods/changeSort');
        if(IS_AJAX){
            //查询参数
            $name = I('name','','trim');
            $status = I('status','','trim');

            //查询条件拼接
            if( $name !='' ) {
                $where['name'] = array('like','%'.$name.'%');
            }
            
            if( $status !='' ) {
                $where['status'] = array('eq',$status);
            }

            $where['typeId'] = $tid;
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
            $db = M('Spec');
            $lists = $db->where($where)
                    ->limit($start,$length)
                    ->order($order)
                    ->select();
            foreach ($lists as $k => &$v) {
            	$items = D('Spec')->getSpecItem($v['id']);
            	$v['items'] = implode(',', $items);
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
     * [addSpec 添加编辑规格项]
     */
    public function addSpec(){
    	if(IS_POST){	
			//接收数据
			$id = I('id',0,'intval');
			M()->startTrans();
            $addResult = D('Spec')->update();
            if($id == 0){
            	$id = $addResult;
            }
            if( !$addResult ) {
            	M()->rollback();
                $this->wrong(D('Spec')->getError());
            } else {
            	//保存规格项
            	if(!D('Spec')->afterSave($id)){
            		M()->rollback();
            		$this->wrong('保存失败');
            	}
            	M()->commit();
                $this->ok('保存成功');
            }
		}else{
			$tid = I('tid',0,'intval');
			$id = I('id',0,'intval');
			if($id){
				$info = M('Spec')->where(['id'=>$id])->find();
				$items = D('Spec')->getSpecItem($id);
				if($items){
					$info['items'] = trim(implode(PHP_EOL, $items));
				}
				$this->info = $info;
			}
			$this->tid = $tid;
			$this->display();
		}
    }

    /**
     * 双击编辑信息
     */
    public function editSpec(){
        $id = I('id',0,'intval');
        $data['name'] = I('name','','trim');
        $rec = M('Spec')->where(['name'=>$data['name']])->find();
        if($rec && $rec['id']!=$id){
        	$this->wrong('该名称已存在');
        }
        $editRec = M('Spec')->where(['id'=>$id])->save($data);
        if(!$editRec){
        	$this->wrong('操作失败');
        }
        $this->ok('操作成功');
    }

    /**
     * 修改规格排序
     */
    public function changeSort(){
        $res = D('Spec')->changeSort();
        if( !$res ) {
            $this->wrong(D('Spec')->getError());
        } else {
            $this->ok('操作成功');
        }
    }

    /**
     * [delSpec 删除规格]
     * @return [type] [description]
     */
    public function delSpec(){
    	$id = I('id',0,'intval');
        $rec = M('Spec')->where(['id'=>$id])->find();
        if(empty($rec)){
            $this->wrong('信息不存在');
        }
        // $rec = M('Goods')->where('typeId',$id)->find();
        
        // if($rec){
        //     $this->wrong('操作失败，该模型下存在商品');
        // }
        $res = M('Spec')->delete($id);
        if(!$res){
            $this->wrong('操作失败');
        }
        $this->ok('操作成功');
    }


    
    /**
     * [ajaxGetCates ajax获取二级分类]
     * @return [type] [description]
     */
    public function ajaxGetCates(){
        $fid = I('fid',0,'intval');
        exit(getCates($fid,1));
    }
}