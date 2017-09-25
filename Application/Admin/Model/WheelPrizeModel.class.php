<?php
namespace Admin\Model;
use Think\Model;
/**
* 抽奖奖品模型
*/
class WheelPrizeModel extends Model
{
	
	public function update($dataArr){
		$tempArr = $this->getField('pid,level,chance');
		$pArr = array_keys($tempArr);
		$optFlag = false;
        foreach ($dataArr as $key => $val) {
			if(in_array($val['pid'], $pArr)){
				$val['opt'] = 'save';
				if($val['level'] == $tempArr[$val['pid']]['level'] && $val['chance'] == $tempArr[$val['pid']]['chance']){
					$val['flag'] = 0;
				}else{
					$val['updateTime'] = time();
					$val['flag'] = 1;
					$optFlag = true;
				}
				$optData[] = $val;
                $index = array_search($val['pid'],$pArr);
                unset($pArr[$index]);
			}else{
				$val['updateTime'] = time();
				$addData[] = $val;
			}
		}
		if(!empty($pArr)){
            foreach ($pArr as $key => $value) {
                $oneData['pid'] = $value;
                $oneData['opt'] = 'del';
                $optData[] = $oneData;
                $optFlag = true;
            }
        }
        $flag = true;
        if($optFlag){
        	foreach ($optData as $k => $v) {
        		if($v['opt']=='save' && $v['flag']){
        			$pres = $this->where(array('pid'=>$v['pid']))->save($v);
        			if(!$pres){
        				$flag = false;
        				return false;
        			}
        		}elseif($v['opt'] == 'del'){
        			$pres = $this->where(array('pid'=>$v['pid']))->delete();
        			if(!$pres){
        				$flag = false;
        				return false;
        			}
        		}
        	}
        }
        if(!empty($addData)){
    		$res = $this->addAll($addData);
    		if(!$res) $flag = false;
    	}
        return $flag;
	}
}