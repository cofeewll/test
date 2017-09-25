<?php
namespace Shop\Model;
/**
* 规格模型
*/
class SpecModel extends BaseModel
{
	
	/* 自动验证规则 */
    protected $_validate = array(
        array('name', 'require', '规格名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', 'checkName', '规格名称已存在', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
        array('items', 'require', '规格项不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('typeId', 'require', '所属模型不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    protected function checkName(){
    	$typeId = $_POST['typeId'];
    	$name = $_POST['name'];
    	$id = $_POST['id'];
    	$rec = $this->where(['typeId'=>$typeId,'name'=>$name])->find();
    	if($rec && $rec['id']!= $id){
    		return false;
    	}
    	return true;
    }

    /**
     * 后置操作方法
     * 自定义的一个函数 用于数据保存后做的相应处理操作, 使用时手动调用
     * @param int $id 规格id
     */
    public function afterSave($id)
    {
        
        $model = M("SpecItem"); // 实例化User对象
        $post_items = explode(PHP_EOL, I('items'));
        foreach ($post_items as $key => $val)  // 去除空格
        {
            $val = str_replace('_', '', $val); // 替换特殊字符
            $val = str_replace('@', '', $val); // 替换特殊字符
            
            $val = trim($val);
            if(empty($val)) 
                unset($post_items[$key]);
            else                     
                $post_items[$key] = $val;
        }
        $db_items = $model->where("specId = $id")->getField('id,item');
        // 两边 比较两次
        
        /* 提交过来的 跟数据库中比较 不存在 插入*/
        foreach($post_items as $key => $val)
        {
            if(!in_array($val, $db_items))            
                $dataList[] = array('specId'=>$id,'item'=>$val);            
        }
        // 批量添加数据
        if($dataList){
        	$addRes = $model->addAll($dataList);
        	if(!$addRes){
        		return false;
        	}
        }
        
        /* 数据库中的 跟提交过来的比较 不存在删除*/
        foreach($db_items as $key => $val)
        {
            if(!in_array($val, $post_items))       
            {       
                //  SELECT * FROM `tp_spec_goods_price` WHERE `key` REGEXP '^11_' OR `key` REGEXP '_13_' OR `key` REGEXP '_21$'
                $res1 = M("GoodsSpec")->where("`key` REGEXP '^{$key}_' OR `key` REGEXP '_{$key}_' OR `key` REGEXP '_{$key}$' or `key` = '{$key}'")->delete(); // 删除规格项价格表
                $res2 = M("SpecItem")->where('id='.$key)->delete(); // 删除规格项
                if(!$res1 || !$res2){
                	return false;
                }
            }
        }
        return true;        
    } 

    /**
     * [getSpecItem 获取规格值]
     * @param  [int] $id [规格id]
     * @return [array]     [规格值数组]
     */
    public function getSpecItem($id){
    	return M('specItem')->where(['specId'=>$id])->getField('item',true);
    }   
}