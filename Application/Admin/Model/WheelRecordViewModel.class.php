<?php

namespace Admin\Model;
use Think\Model\ViewModel;

/**
* 抽奖记录
*/
class WheelRecordViewModel extends ViewModel{
	
	public $viewFields = array(

		'wheel_record' => array('id','uid','fid','pid','createTime','isDeal','_as'=>'r','_type'=>'LEFT'),
		'user' => array('nickname','number','_on'=>'r.uid=user.id','_type'=>'LEFT'),
		'prize' => array('name','type','amount','_on'=>'prize.id=r.pid','_type'=>'LEFT'),
	);
}