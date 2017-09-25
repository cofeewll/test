<?php
namespace Admin\Model;


class GoodsCateModel extends BaseModel
{
    /* 自动验证规则 */
    protected $_validate = array(
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('fid', 'require', '上级不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '', '名称已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('img','checkImg','请上传图片',self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('addTime', NOW_TIME, self::MODEL_INSERT),
        array('updateTime', NOW_TIME, self::MODEL_BOTH),
    );

    protected function checkImg(){
        if($_POST['fid']>0 && $_POST['img'] == ''){
            return false;
        }
        return true;
    }
}