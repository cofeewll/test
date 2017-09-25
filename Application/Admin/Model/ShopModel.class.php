<?php
namespace Admin\Model;

/**
* 商家模型
*/
class ShopModel extends BaseModel
{
	
	/* 自动验证规则 */
    protected $_validate = array(
        array('title', 'require', '商家名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '', '该商家名称已存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('username', 'require', '商家账号不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('password', 'checkPwd', '登录密码不能为空', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
        array('password', '6,16', '登录密码长度必须在6-16位之间', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
        array('phone', 'require', '手机号不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('phone', 'checkPhone', '手机号格式不正确', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
        array('phone', '', '该手机号已存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('username', '', '该账号名称已存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('shopFee', 'checkFee', '手续费格式不正确', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
    	array('password','getPwd',self::MODEL_BOTH,'callback'),
        array('regTime', NOW_TIME, self::MODEL_INSERT),
        array('updateTime', NOW_TIME, self::MODEL_BOTH),
    );

    protected function checkFee(){
        $fee = floatval($_POST['shopFee']);
        if($fee<0 || $fee>1){
            return false;
        }
        return true;
    }

}