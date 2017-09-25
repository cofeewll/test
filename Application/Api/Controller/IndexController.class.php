<?php
namespace Api\Controller;
/**
* 首页相关接口
*/
class IndexController extends BaseController
{
	
	/**
	 * [getBannerList 获取Banner列表]
	 * @return [type] [description]
	 */
	public function getBannerList(){
		$lists = M('Banner')->field('id,img')
				->where(['status'=>1])
				->order('sorts asc,id asc')
				->select();
		foreach ($lists as $key => &$value) {
			$value['img'] = C('SITE_ROOT').$value['img'];
		}
		ajax_return_ok($lists);
	}

	/**
	 * [getGoldRank 获取金币排行榜]
	 * @return [type] [description]
	 */
	public function getGoldRank(){
		$page = I('page',1,'intval');
		$pageSize = I('pageSize',10,'intval');
		$model = M('User');
		$count = $model->count();
		$pageCount = ceil($count/$pageSize);
		$offset = ($page-1)*$pageSize;
		$lists = $model->field('id,number,nickname,gold')
				->order('gold desc,id asc')
				->limit($offset,$pageSize)
				->select();
		foreach ($lists as $key => $value) {
			$lists[$key]['No'] = $key+1;
		}
		ajax_return_ok($lists);
	}

	/**
	 * [getIndexNotices 获取首页最新中奖信息]
	 * @return [type] [description]
	 */
	public function getIndexNotices(){
		$size = I('size',10,'intval');
		$lists = M('WheelRecord')->alias('r')
				->field('r.id,number,nickname,name')
				->join('wg_user on r.uid = wg_user.id','left')
				->where(['fid'=>0])
				->order('r.createTime desc')
				->limit($size)
				->select();
		// foreach ($lists as $key => &$value) {
		// 	if($value['type'] == 2){
		// 	}
		// }
		ajax_return_ok($lists);		
	}

	/**
	 * [getNotices 获取中奖公告列表]
	 * @return [type] [description]
	 */
	public function getNotices(){
		$page = I('page',1,'intval');
		$pageSize = I('pageSize',10,'intval');
		$model = M('WheelNotice');
		$count = $model->count();
		$pageCount = ceil($count/$pageSize);
		$offset = ($page-1)*$pageSize;
		$lists = $model->field('id,title,content,createTime')
				->order('createTime desc')
				->limit($offset,$pageSize)
				->select();
		foreach($lists as $k=>&$v){
			$v['content'] = htmlspecialchars_decode($v['content']);
			preg_match_all('/<img[^>]*src\s*=\s*([\'"]?)([^\'" >]*)\1/isu', $v['content'], $src);
	        foreach ($src[2] as $key => $value) {
	            $pos1 = strpos($value, 'http://');
	            $pos2 = strpos($value, 'https://');
	            if ($pos1 === false&&$pos2 === false) {
	                $res = str_replace($value, C('SITE_ROOT').$value, $v['content']);
	                $v['content'] = $res;
	            }
	        }
	        $v['content'] = str_replace('<img', '<img width="100%" ', $v['content']);
	        $v['desc'] = msubstr(strip_tags($v['content']),0,40);
	        $v['createTime'] = date('Y-m-d',$v['createTime']);
		}
		ajax_return_ok($lists);
	}

}