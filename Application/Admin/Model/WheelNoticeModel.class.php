<?php
namespace Admin\Model;
/**
* 中奖公告
*/
class WheelNoticeModel extends BaseModel
{
	
	/* 自动验证规则 */
    protected $_validate = array(
        array('title', 'require', '公告标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', 'require', '公告内容不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('createTime', NOW_TIME, self::MODEL_INSERT),
        array('updateTime', NOW_TIME, self::MODEL_BOTH),
    );

}