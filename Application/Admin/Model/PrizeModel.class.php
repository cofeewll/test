<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/31
 * Time: 16:03
 */
namespace Admin\Model;
/**
 * 奖品模型
 * Class PrizeModel
 * @package Admin\Model
 */
class PrizeModel extends BaseModel
{
    /* 自动验证规则 */
    protected $_validate = array(
        array('name', 'require', '奖品名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '', '该奖品名称已存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('cate', 'require', '请选择奖品分类', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('type', 'checkType', '请选择奖品类型', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
        array('amount', 'checkAmount', '请输入奖励金币数额', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('amount', 'getAmount', self::MODEL_BOTH,'callback'),
        array('addTime', NOW_TIME, self::MODEL_INSERT),
        array('updateTime', NOW_TIME, self::MODEL_BOTH),
    );

    protected function getAmount(){
        if($_POST['cate'] == 2){
            return 0;
        }elseif($_POST['type'] == 1){
            return 0;
        }else{
            return $_POST['amount'];
        }
    }

    protected function checkType(){
        $cate = $_POST['cate'];
        if($cate == 1 && $_POST['type']==''){
            return false;
        }
        return true;
    }

    protected function checkAmount(){
        $type = intval($_POST['type']);
        if( $type == 2){
            if( trim($_POST['amount']) == ''){
                return false;
            }
        }
        return true;
    }

     /**
     * 修改数据状态 status
     *
     */

    public function changeStatus(){
        $id = intval($_POST['id']);
        $value = intval($_POST['value']);
        if(!$id){
            $this->error = '缺失参数';
            return false;
        }
        $rec = M('WheelPrize')->where('pid',$id)->find();
        if($rec){
            $this->error = '操作失败，该奖品正在参与抽奖活动';
            return false;
        }
        if($value == 0){
            $data = array('status'=>1);
        }elseif($value == 1){
            $data = array('status'=>0);
        }else{
            $this->error = '缺失参数';
            return false;
        }
        $data['id'] = $id;
        $optRes = $this->save($data);
        if($optRes === false){
            $this->error = '操作失败';
            return false;
        }
        return true;
    }
}