<?php

namespace Admin\Model;
use Think\Model\ViewModel;

/**
* 管理员视图
*/
class AdminViewModel extends ViewModel{
	
	public $viewFields = array(

		'admin' => array('id','username','mobile','loginTime','status','loginIp','password','_type'=>'LEFT'),
		'auth_group_access' => array('uid','group_id','_on'=>'admin.id=auth_group_access.uid','_type'=>'LEFT'),
		'auth_group' => array('title','_on'=>'auth_group.id=auth_group_access.group_id','_type'=>'LEFT'),
	);
}