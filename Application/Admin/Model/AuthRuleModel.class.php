<?php
namespace Admin\Model;
/**
* 权限模型
*/
class AuthRuleModel extends BaseModel
{
	
	/* 自动验证规则 */
    protected $_validate = array(
        array('title', 'require', '权限名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', 'require', '权限标识不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '', '权限标识已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
    );

}