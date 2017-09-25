<?php
namespace Admin\Model;
/**
* 文章模型
* @author   <[wanglinlin]>
*/
class ArticleModel extends BaseModel
{
	
	/* 自动验证规则 */
    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '', '标题已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('type', 'require', '类型不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('context', 'require', '内容不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('addTime', NOW_TIME, self::MODEL_INSERT),
        array('updateTime', NOW_TIME, self::MODEL_BOTH),
    );

}