<?php
namespace Api\Controller;

/**
* 商家信息接口
*/
class ShopController extends BaseController
{
	
	/**
	 * [getShopCates 获取商家店铺分类]
	 * @return [type] [description]
	 */
	public function getCates(){
		$shopId = I('shopId',0,'intval');
		if(!$shopId){
			ajax_return_error('',9);
		}
		$cateIds = M('Goods')->where(['shopId'=>$shopId,'status'=>1])->getField('cid',true);
		$cateIds = array_unique($cateIds);
		$lists = M('goodsCate')->field('id,name')
				->where(['id'=>['in',$cateIds]])
				->order('sorts asc,id asc')
				->select();
		ajax_return_ok($lists);
	}

	/**
	 * [getIndex 获取店铺首页信息]
	 * @return [type] [description]
	 */
	public function getIndex(){
		$shopId = I('shopId',0,'intval');
		if(!$shopId){
			ajax_return_error('',9);
		}
		$data = [];
		$data = M('Shop')->field('title,img,detail,tel,qq')
				->where(['id'=>$shopId])->find();
		$data['img'] = C('SITE_ROOT').$data['img'];
		$banners = M('picture')->where(['type'=>3,'sid'=>$shopId])->getField('path',true);
		foreach ($banners as $k => &$v) {
			$v = C('SITE_ROOT').$v;
		}
		$data['banners'] = $banners?$banners:[];
		$data['sellNum'] = M('orderGoods')->where(['shopId'=>$shopId,'addTime'=>strtotime(date('Y-m-01'))])->sum('number');
		//图文信息过滤
		$detail = htmlspecialchars_decode($data['detail']);
		preg_match_all('/<img[^>]*src\s*=\s*([\'"]?)([^\'" >]*)\1/isu', $detail, $src);
        foreach ($src[2] as $key => $value) {
            $pos1 = strpos($value, 'http://');
            $pos2 = strpos($value, 'https://');
            if ($pos1 === false&&$pos2 === false) {
                $res = str_replace($value, C('SITE_ROOT').$value, $detail);
                $detail = $res;
            }
        }
        $data['detail'] = str_replace('<img', '<img width="100%" ', $detail);
		ajax_return_ok($data);
	}

	/**
	 * [getInfo 获取商家详情]
	 * @return [type] [description]
	 */
	public function getInfo(){
		$shopId = I('shopId',0,'intval');
		if(!$shopId){
			ajax_return_error('',9);
		}
		$data = [];
		$data = M('Shop')->field('address,description,tel,license')
				->where(['id'=>$shopId])->find();
		$data['license'] = C('SITE_ROOT').$data['license'];
		$album = M('picture')->where(['type'=>2,'sid'=>$shopId])->getField('path',true);
		foreach ($album as $k => &$v) {
			$v = C('SITE_ROOT').$v;
		}
		$data['album'] = $album ? $album :[];
		ajax_return_ok($data);
	}
}