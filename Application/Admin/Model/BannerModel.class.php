<?php
namespace Admin\Model;
/**
* Banner模型
* @author   <[wanglinlin]>
*/
class BannerModel extends BaseModel
{
	
	/* 自动验证规则 */
    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('img', 'require', '图片不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '', '标题已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        // array('content', 'require', '资讯内容不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('addTime', NOW_TIME, self::MODEL_INSERT),
        array('updateTime', NOW_TIME, self::MODEL_BOTH),
    );

}