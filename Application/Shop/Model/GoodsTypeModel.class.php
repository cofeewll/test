<?php
namespace Shop\Model;
/**
* 商品模型
*/
class GoodsTypeModel extends BaseModel
{
	
	/* 自动验证规则 */
    protected $_validate = array(
        array('name', 'require', '模型名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('shopId', 'require', '商家不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '', '模型名称已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
    );
    
}