<?php
namespace Admin\Model;
/**
* 角色模型
*/
class AuthGroupModel extends BaseModel
{
	
	/* 自动验证规则 */
    protected $_validate = array(
        array('title', 'require', '角色名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('rules', 'checkRule', '角色权限不能为空', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
        array('title', '', '角色名称已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
    	array('rules','getRule',self::MODEL_BOTH,'callback'),
    );

    protected function checkRule(){
        $rule = implode(',', $_POST['rules']);
        if(empty($rule)){
            return false;
        }
        return true;
    }

    protected function getRule(){
    	return implode(',', $_POST['rules']);
    }
}