<?php
namespace Api\Controller;
use Common\Util\TreeUtil;

/**
* 商品相关接口
*/
class GoodsController extends CenterController
{
	
	/**
	 * [getCates 获取商品分类信息]
	 * @return [type] [description]
	 */
	public function getCates(){
		$lists = M('GoodsCate')->field('id,fid,name,img')->where(['status'=>1])->select();
		foreach ($lists as $key => &$value) {
			$value['img'] = $value['img'] ? C('SITE_ROOT').$value['img']:'';
		}
		$lists = TreeUtil::list_to_tree($lists,0,'id','fid');
		ajax_return_ok($lists);
	}

	/**
	 * [getList 获取新品/热卖商品列表]
	 * @return [type] [description]
	 */
	public function getList(){
		$page = I('page',1,'intval');
		$pageSize = I('pageSize',10,'intval');
		$type = I('type',1,'intval');	//1-热销商品2=新品
		$shopId = I('shopId',0,'intval');
		$map = ['status'=>1];
		
		if($shopId>0){
			$map['shopId'] = $shopId;
			if($type == 2){
				$order = 'isIndex desc,sorts asc,createTime desc';
			}else{
				$order = 'isIndex desc,sorts asc,sellNum desc';
			}
		}else{
			if($type == 2){
				$order = 'isNew desc,sort asc,createTime desc';
			}else{
				$order = 'isHot desc,sort asc,sellNum desc';
			}
		}
		$data = $this->getGoodsList($map,$order,$page,$pageSize);
  		ajax_return_ok($data);
	}

	/**
	 * [getAll 获取商品列表；筛选条件:关键字、商家、分类；排序方式：综合、销量、价格、库存]
	 * @return [type] [description]
	 */
	public function getAll(){
		$page = I('page',1,'intval');
		$pageSize = I('pageSize',10,'intval');
		$order = I('order','mix','trim');
		$sort = I('sort','asc','trim');
		$shopId = I('shopId',0,'intval');
		$cateId = I('cateId',0,'intval');
		$keyword = I('keyword','','trim');

		$map = ['status'=>1];

		if(!empty($keyword)){
			if(strlen($keyword)>100){
				ajax_return_error('关键词长度过长');
			}
			$res = $this->addHistory($keyword);
			if(!$res){
				ajax_return_error('系统出错啦');
			}
			$map['name'] = ['like','%'.$keyword.'%'];
		}
		$ord = '';
		if($shopId){
			$map['shopId'] = $shopId;
			if($order == 'mix'){
				$ord = 'sorts '.$sort.',sellNum desc,price asc,createTime desc';
			}else{
				$ord = $order.' '.$sort.',sorts asc,createTime desc';
			}
		}else{
			if($order == 'mix'){
				$ord = 'sort '.$sort.',sellNum desc,price asc,createTime desc';
			}else{
				$ord = $order.' '.$sort.',sort asc,createTime desc';
			}
		}
		if($cateId){
			$map['cid'] = $cateId;
		}

		$data = $this->getGoodsList($map,$ord,$page,$pageSize);
		ajax_return_ok($data);
	}

	/**
	 * [getGoods 获取商品详情]
	 * @return [type] [description]
	 */
	public function getGoods(){
		$id = I('id',0,'intval');
		$size = I('size',3,'intval');
		if(!$id){
			ajax_return_error('',9);
		}
		//基本信息、商家信息
		$info = M('Goods')->alias('g')->field('g.id,name,goodsSn,g.img,price,stock,g.sellNum,commentNum,shopId,g.detail,title,s.img as shopImg,s.sellNum as shopNum,qq')
			->join('wg_shop as s on s.id = g.shopId')
			->where(['g.id'=>$id,'g.status'=>1])->find();
		if(empty($info)){
			ajax_return_error('商品信息已下架');
		}
		$info['img'] = C('SITE_ROOT').$info['img'];
		$info['shopImg'] = C('SITE_ROOT').$info['shopImg'];
		//相册信息
		$album = M('picture')->where(['sid'=>$id,'type'=>1])->getField('path',true);
		foreach ($album as $key => &$val) {
			$val = C('SITE_ROOT').$val;
		}
		$info['album'] = $album;
		//评价信息
		$coms = M('OrderComment')->alias('c')->field('context,reply,spec_key_name,nickname,img,c.createTime,replyTime,imgs')
			->join('wg_user as u on u.id = c.uid')
			->where(['goodsId'=>$id,'isShow'=>1])
			->order('createTime desc')
			->limit($size)->select();
		foreach ($coms as $k => &$v) {
			$v['img'] = C('SITE_ROOT').$v['img'];
			$v['createTime'] = date('Y-m-d',$v['createTime']);
			$v['replyTime'] = date('Y-m-d',$v['replyTime']);
			$v['spec'] = getSpec($v['spec_key_name']);
			if($v['imgs']){
				$imgs = explode('|',$v['imgs']);
				if(is_array($imgs)){
					foreach ($imgs as $key => &$img) {
						$img = C('SITE_ROOT').$img;
					}
					$v['imgs'] = $imgs;
				}else{
					$v['imgs'][] = C('SITE_ROOT').$imgs;
				}
			}else{
				$v['imgs'] = [];
			}
			
		}
		$info['coms'] = $coms;
		//图文信息过滤
		$detail = htmlspecialchars_decode($info['detail']);
		preg_match_all('/<img[^>]*src\s*=\s*([\'"]?)([^\'" >]*)\1/isu', $detail, $src);
        foreach ($src[2] as $key => $value) {
            $pos1 = strpos($value, 'http://');
            $pos2 = strpos($value, 'https://');
            if ($pos1 === false&&$pos2 === false) {
                $res = str_replace($value, C('SITE_ROOT').$value, $detail);
                $detail = $res;
            }
        }
        $info['detail'] = str_replace('<img', '<img width="100%" ', $detail);
        //商品规格
        $info['specArr'] = D('Goods')->get_spec($id);
        //商品规格数据
        $info['specInfo'] = M('GoodsSpec')->where(['goodsId'=>$id])->field('key,price,store')->select();
        // $info['spec_goods_price']  = M('GoodsSpec')->where("goodsId=".$id)->getField("key,price,store"); // 规格 对应 价格 库存表
        //是否收藏
        $uid = $this->uid;
        $rec = M('collect')->where(['uid'=>$uid,'goodsId'=>$id,'type'=>1])->find();
        $info['isCollect'] = $rec?1:0;

        $num = M('orderGoods')->where(['goodsId'=>$id,'addTime'=>strtotime(date('Y-m-01'))])->sum('number');
        $snum = M('orderGoods')->where(['shopId'=>$info['shopId'],'addTime'=>strtotime(date('Y-m-01'))])->sum('number');
        $info['sellNum'] = $num ? $num :0;
        $info['shopNum'] = $snum ? $snum :0;
		ajax_return_ok($info);
	}

	/**
	 * [getGoodsSpec 获取商品规格]
	 * @return [type] [description]
	 */
	public function getGoodsSpec(){
		$id = I('id',0,'intval');
		$key = I('key','','trim');
		if(!$id || $key == ''){
			ajax_return_error('',9);
		}
		$key = trim(str_replace('|', '_', $key),'_');
		$rec = M('GoodsSpec')->where(['goodsId'=>$id,'key'=>$key])->field('id as gsId,goodsId,key,key_name,price,store')->find();
		if( empty($rec) || $rec['store']<=0 ){
			ajax_return_error('库存不足');
		}
		$name = explode(' ', $rec['key_name']);
		$key_name = [];
		$key_value = [];
		foreach ($name as $key => $value) {
			$temp = explode(':', $value);
			$tem['name'] = $temp[0];
			$tem['value'] = $temp[1];
			$key_value[] = $temp[1];
			$key_name[] = $tem;
		}
		$rec['spec_value'] = $key_value;
		$rec['spec'] = $key_name;
		unset($rec['key_name']);
		ajax_return_ok($rec);
	}

	/**
	 * [collect 添加/取消商品收藏]
	 * @return [type] [description]
	 */
	public function collect(){
		$uid = $this->uid;
		$id = I('id',0,'intval');
		if(!$id){
			ajax_return_error('',9);
		}
		$model = M('collect');
		$data = ['uid'=>$uid,'goodsId'=>$id,'type'=>1];
		$rec = $model->where($data)->find();
		if($rec){
			if($model->where(['id'=>$rec['id']])->delete()){
				ajax_return_ok(['isCollect'=>0],'取消收藏成功');
			}else{
				ajax_return_error('操作失败');
			}
		}else{
			if($model->add($data)){
				ajax_return_ok(['isCollect'=>1],'收藏成功');
			}else{
				ajax_return_error('操作失败');
			}
		}
	}

	/**
	 * [getComments 获取商品评价]
	 * @return [type] [description]
	 */
	public function getComments(){
		$id = I('id',0,'intval');
		$page = I('page',1,'intval');
		$pageSize = I('pageSize',10,'intval');
		if(!$id){
			ajax_return_error('',9);
		}
		$map = ['goodsId'=>$id,'isShow'=>1];
		$count = M('OrderComment')->where($map)->count();
		$pageCount = ceil($count/$pageSize);
		$offset = ($page-1)*$pageSize;
		$lists = M('OrderComment')->alias('c')->field('context,reply,spec_key_name,nickname,img,c.createTime,replyTime,imgs')
			->join('wg_user as u on u.id = c.uid')
			->where($map)
			->order('createTime desc')
			->limit($offset,$pageSize)->select();
		foreach ($lists as $k => &$v) {
			$v['img'] = C('SITE_ROOT').$v['img'];
			$v['createTime'] = date('Y-m-d',$v['createTime']);
			$v['replyTime'] = date('Y-m-d',$v['replyTime']);
			$v['spec'] = getSpec($v['spec_key_name']);
			if($v['imgs']){
				$imgs = explode('|',$v['imgs']);
				if(is_array($imgs)){
					foreach ($imgs as $key => &$img) {
						$img = C('SITE_ROOT').$img;
					}
					$v['imgs'] = $imgs;
				}else{
					$v['imgs'][] = C('SITE_ROOT').$imgs;
				}
			}else{
				$v['imgs'] = [];
			}
		}
		$data = [
			'page'=>$page,
			'pageCount'=>$pageCount,
			'lists'=>$lists
		];
		ajax_return_ok($data);
	}

	/**
	 * [getHistory 获取最近一个月搜索列表]
	 * @return [type] [description]
	 */
	public function getHistory(){
		$size = I('size',10,'intval');
		$uid = $this->uid;
		$time = strtotime("-1 month");
		$map = ['uid'=>$uid,'createTime'=>['gt',$time]];
		$lists = M('History')->field('id,keyword')
				->where($map)
				->order('createTime desc')
				->limit($size)
				->select();
		ajax_return_ok($lists);
	}

	/**
	 * [clearHistory 清除历史记录]
	 * @return [type] [description]
	 */
	public function clearHistory(){
		$uid = $this->uid;
		$res = M('History')->where(['uid'=>$uid])->delete();
		if($res === false){
			ajax_return_error('操作失败');
		}
		ajax_return_ok([],'操作成功');
	}

	/**
	 * [addHistory 添加搜索历史记录]
	 * @param [type] $keyword [搜索关键字]
	 */
	protected function addHistory($keyword){
		$uid = $this->uid;
		$model = M('History');
		$rec = $model->where(['uid'=>$uid,'keyword'=>$keyword])->find();
		if(empty($rec)){
			return $model->add(['uid'=>$uid,'keyword'=>$keyword,'createTime'=>time()]);
		}
		return true;
	}
	

	/**
	 * [getGoodsList 获取商品列表]
	 * @param  [type] $map    [description]
	 * @param  [type] $order    [description]
	 * @param  [type] $page     [description]
	 * @param  [type] $pageSize [description]
	 * @return [type]           [description]
	 */
	protected function getGoodsList($map,$order,$page,$pageSize){
		$model = M('Goods');
		$count = $model->where($map)->count();
		$pageCount = ceil($count/$pageSize);
		$offset = ($page-1)*$pageSize;
		$lists = $model->field('id,img,name,price,sellNum,stock,createTime')
				->where($map)
				->order($order)
				->limit($offset,$pageSize)
				->select();
		foreach ($lists as $key => &$value) {
			$value['img'] = C('SITE_ROOT').$value['img'];
			$num = M('orderGoods')->where(['goodsId'=>$value['id'],'addTime'=>strtotime(date('Y-m-01'))])->sum('number');
			$value['sellNum'] = $num?$num:0;
		}
		$data = [
			'lists'=>$lists,
			'page'=>$page,
			'pageCount'=>$pageCount
		];
		return $data;
	}

	
}