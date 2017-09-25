<?php
namespace Api\Controller;

/**
* 转盘接口
*/
class WheelController extends CenterController
{
	public $config ;	//抽奖配置

	public function __construct(){
        parent::__construct();
        $config = M('config')->where(['flag'=>2])->getField('config,value');
		$this->config = $config;
    }

	/**
	 * [getInfo 获取转盘基本信息-规则、奖品列表]
	 * @return [type] [description]
	 */
	public function getInfo(){
		$config = $this->config;
		$rule = htmlspecialchars_decode($config['rule']);
		$lists = M('WheelPrize')->alias('p')->field('p.pid,level,name')
				->join('wg_prize on wg_prize.id = p.pid')
				->select();

		$data = ['rule'=>$rule,'lists'=>$lists,'status'=>$config['status']];
		ajax_return_ok($data);
	}

	/**
	 * [getPrize 抽奖]
	 * @return [type] [description]
	 */
	public function getPrize(){
		$uid = $this->uid;
		$config = $this->config;
		if(!$config['status']){
			ajax_return_error('抽奖活动已暂停');
		}
		$uinfo = M('User')->where(['id'=>$uid])->find();
		if($uinfo['score'] < $config['score']){
			ajax_return_error('您的积分不足');
		}
		$prizeArr = M('WheelPrize')->field('pid,level,chance')->select();
		$result = $this->get_rand($prizeArr);

		$info = M('prize')->where(['id'=>$result['pid']])->field('id,name,type,amount')->find();
		$rec = [
			'uid'=>$uid,
			'pid'=>$result['pid'],
			'name'=>$info['name'],
			'fid'=>0,
			'createTime'=>time()
		];
		M()->startTrans();
		if($info['type'] == 2){	//金币-直接向用户增加金币
			$addRes = M('User')->where(['id'=>$uid])->setInc('gold',$info['amount']);
			if(!$addRes){
				M()->rollback();
				ajax_return_error('操作失败');
			}
			$rec['isDeal'] = 1;
			$rec['amount'] = $info['amount'];
		}elseif($info['type'] == 1){	//特殊奖-向平台管理员生成消息
			$data = [
				'title'=>'用户抽中特殊奖',
				'content'=>'用户 ID:'.$uinfo['number'].' / '.$uinfo['nickname'].'抽中'.$info['name'].'(特殊奖),请尽快派奖。',
				'createTime'=>time(),
			];
			if(!M('message')->add($data)){
				M()->rollback();
				ajax_return_error('操作失败');
			}
			$rec['isDeal'] = 0;
			$rec['amount'] = 0;
		}
		//增加抽奖记录，减用户积分

		if(!M('WheelRecord')->add($rec)){
			M()->rollback();
			ajax_return_error('操作失败');
		}
		$decRes = M('User')->where(['id'=>$uid])->setDec('score',$config['score']);
		if(!$decRes){
			M()->rollback();
			ajax_return_error('操作失败');
		}
		M()->commit();
		unset($info['type']);
		unset($info['amount']);
		ajax_return_ok($info);
	}


	 /* 
	     * 经典的概率算法， 
	     * $proArr是一个预先设置的数组， 
	     * 假设数组为：array(100,200,300，400)， 
	     * 开始是从1,1000 这个概率范围内筛选第一个数是否在他的出现概率范围之内，  
	     * 如果不在，则将概率空间，也就是k的值减去刚刚的那个数字的概率空间，  
	     */  
    
    protected function get_rand($proArr) {   
        $result = array();
        foreach ($proArr as $key => $val) { 
            $arr[$key] = $val['chance']; 
        } 
        // 概率数组的总概率  
        $proSum = array_sum($arr);        
        asort($arr);
        // 概率数组循环   
        foreach ($arr as $k => $v) {   
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $v) {   
                $result = $proArr[$k]; 
                break;   
            } else {   
                $proSum -= $v;   
            }         
        }
        unset ($arr);   
        return $result;   
    } 
}