<?php
namespace Common\Model;
use Admin\Model\BaseModel;
/**
* 商品模型
*/
class GoodsModel extends BaseModel
{
	/* 自动验证规则 */
	protected $_validate = array(
		array('name', 'require', '商品名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('name', '', '商品名称已存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('cid', 'require', '商品分类不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('goodsSn', '', '商品货号已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('img', 'require', '封面图不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('images', 'checkAlbum', '相册不能为空', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
        array('detail', 'require', '详情不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('price', 'require', '价格不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('stock', 'require', '库存不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('shopId', 'require', '商家不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	/* 自动完成规则 */
	protected $_auto = array(
        array('status','getStatus',self::MODEL_UPDATE, 'callback'),
		array('createTime', NOW_TIME, self::MODEL_INSERT),
        array('updateTime', NOW_TIME, self::MODEL_BOTH),
	);

	//检查相册
    protected function checkAlbum(){
        $album = $_POST['images']?$_POST['images']:'';
        if(empty($album)||!is_array($album)){
            return false;
        }
        return true;
    }

    protected function getStatus(){
        //拒绝状态修改为待审核
        $id = $_POST['id'];
        $oldStatus = M('Goods')->where(['id'=>$id])->getField('status');
        if($oldStatus>=-1){
            return $oldStatus;
        }else{
            return -1;
        }
    }

    /**
     * 后置操作方法
     * 自定义的一个函数 用于数据保存后做的相应处理操作, 使用时手动调用
     * @param int $goods_id 商品id
     */
    public function afterSave($goods_id)
    {            
         // 商品货号
         $goods_sn = "WG".str_pad($goods_id,6,"0",STR_PAD_LEFT);   
         $res = M('Goods')->where("id = $goods_id and goodsSn = ''")->save(array("goodsSn"=>$goods_sn)); // 根据条件更新记录
         if($res === false){
         	return false;
         }        
         // 商品图片相册  图册
         $goods_images = I('images/a');
         if($goods_images){
            $imgRes = $this->upImages($goods_images,$goods_id,1);
             if(!$imgRes){
                return false;
             }
         }
         // 商品规格价钱处理        
         $temp = M("GoodsSpec")->where('goodsId = '.$goods_id)->getField('key,price,store'); // 获取原有的价格规格对象         
         $temp_keys = $temp?array_keys($temp):[];
         $items = I('item/a',[]);
         
         if($items)
         {
            $tem_keys = array_keys($items);
            $delArr = array_diff($temp_keys, $tem_keys);
            $dataList = [];            
             foreach($items as $k => $v)
             {
             	// 批量添加数据
                $v['price'] = floatval($v['price']);
                $v['store'] = intval($v['store']); 
             	if(in_array($k, $temp_keys)){
             		if($v['price'] == 0){
             			$delArr[] = $k;
             		}else{
             			if($v['price']!=$temp[$k]['price'] || $v['store']!=$temp[$k]['store']){
	             			$upRes = M('GoodsSpec')->where(['goodsId'=>$goods_id,'key'=>$k])->save(['price'=>$v['price'],'store'=>$v['store']]);
	             			if($upRes === false){
	             				return false;
	             				break;
	             			}
	             		}
	             		if($v['price']!=$temp[$k]['price']){
	             			// 修改商品后购物车的商品价格也修改一下
		                    $cartRes = M('cart')->where("goodsId = $goods_id and spec_key = '$k'")->save(array(
		                            'price'=>$v['price'], //价格                    
		                            ));
		                    if($cartRes === false){
	             				return false;
	             				break;
	             			}   
		             	}
             		}
             		
             	}else{
             		if($v['price']>0){
             			$dataList[] = ['goodsId'=>$goods_id,'key'=>$k,'key_name'=>$v['key_name'],'price'=>$v['price'],'store'=>$v['store']];
             		}
             	}
                                
             }
             if(!empty($dataList)){
             	$adRes = M("GoodsSpec")->addAll($dataList);   
             	if(!$adRes){
             		return false;
             	}
             }
            
            if($delArr){
            	$res0 = M('GoodsSpec')->where(['goodsId'=>$goods_id,'key'=>['in',$delArr]])->delete();
            	if($res0 === false){
                    return false;
                }
                $res1 = M('cart')->where(['goodsId'=>$goods_id,'spec_key'=>['in',$delArr]])->delete();
                if($res1 === false){
                    return false;
                }
            }
         }else{
         	 $dres = M("GoodsSpec")->where('goodsId = '.$goods_id)->delete();
             if($dres === false){
                return false;
             }
         	 $dres0 = M('cart')->where("goodsId = $goods_id and spec_key <> ''")->delete();
             if($dres0 === false){
                return false;
             }
         }   
         //商品运费处理
         $province = I('province/a');
         $fee = I('fee/a');
         if( $province ){   // 商家设置特殊区域
            $tempFee = M('GoodsShip')->where(['goodsId'=>$goods_id])->getField('province,fee');
             if( $tempFee ){
                $tempKeys = array_keys($tempFee);
             }
             $delPro = [];
             $addList = [];
             foreach ($fee as $key => $val) {
                if(trim($val)!=''){
                    if($tempKeys){
                        if(in_array($province[$key], $tempKeys)){
                            if($val != $tempFee[$province[$key]]){
                                M('GoodsShip')->where(['goodsId'=>$goods_id,'province'=>$province[$key]])->save(['fee'=>$val]);
                            }
                        }
                     }else{
                        $addList[] = ['goodsId'=>$goods_id,'province'=>$province[$key],'fee'=>$val];
                     }
                }else{
                    $delPro[] = $province[$key];
                }
             }
             if($addList){
                if(!M('GoodsShip')->addAll($addList)){
                    return false;
                }
             } 
             if($delPro){
                if(M('GoodsShip')->where(['goodsId'=>$goods_id,'province'=>['in',$delPro]])->delete() === false){
                    return false;
                }
             } 
         }
         
         // 商品规格图片处理
         // if(I('item_img/a'))
         // {    
         //     M('SpecImage')->where("goods_id = $goods_id")->delete(); // 把原来是删除再重新插入
         //     foreach (I('item_img/a') as $key => $val)
         //     {                 
         //         M('SpecImage')->insert(array('goods_id'=>$goods_id ,'spec_image_id'=>$key,'src'=>$val));
         //     }                                                    
         // }
         $stockRes = refresh_stock($goods_id); // 刷新商品库存
         if(!$stockRes){
         	return false;
         }
         return true;
    }

    /**
     * 获取 规格的 笛卡尔积
     * @param $goods_id 商品 id     
     * @param $spec_arr 笛卡尔积
     * @return string 返回表格字符串
     */
    public function getSpecInput($goods_id, $spec_arr)
    {
        // <input name="item[2_4_7][price]" value="100" /><input name="item[2_4_7][name]" value="蓝色_S_长袖" />        
        /*$spec_arr = array(         
            20 => array('7','8','9'),
            10=>array('1','2'),
            1 => array('3','4'),
            
        );  */        
        // 排序
        foreach ($spec_arr as $k => $v)
        {
            $spec_arr_sort[$k] = count($v);
        }
        asort($spec_arr_sort);        
        foreach ($spec_arr_sort as $key =>$val)
        {
            $spec_arr2[$key] = $spec_arr[$key];
        }
     
        
         $clo_name = array_keys($spec_arr2);         
         $spec_arr2 = combineDika($spec_arr2); //  获取 规格的 笛卡尔积                 
                       
         $spec = M('Spec')->getField('id,name'); // 规格表
         $specItem = M('SpecItem')->getField('id,item,specId');//规格项
         $keySpecGoodsPrice = M('GoodsSpec')->where('goodsId = '.$goods_id)->getField('key,key_name,price,store');//规格项
                          
       $str = "<table class='table table-bordered' id='spec_input_tab'>";
       $str .="<tr>";       
       // 显示第一行的数据
       foreach ($clo_name as $k => $v) 
       {
           $str .=" <td><b>{$spec[$v]}</b></td>";
       }    
        $str .="<td><b>价格</b></td>
               <td><b>库存</b></td>
             </tr>";
       // 显示第二行开始 
       foreach ($spec_arr2 as $k => $v) 
       {
            $str .="<tr>";
            $item_key_name = array();
            foreach($v as $k2 => $v2)
            {
                $str .="<td>{$specItem[$v2]['item']}</td>";
                $item_key_name[$v2] = $spec[$specItem[$v2]['specId']].':'.$specItem[$v2]['item'];
            }   
            ksort($item_key_name);            
            $item_key = implode('_', array_keys($item_key_name));
            $item_name = implode(' ', $item_key_name);
            
            $keySpecGoodsPrice[$item_key]['price'] ? false : $keySpecGoodsPrice[$item_key]['price'] = 0; // 价格默认为0
            $keySpecGoodsPrice[$item_key]['store'] ? false : $keySpecGoodsPrice[$item_key]['store'] = 0; //库存默认为0
            $str .="<td><input name='item[$item_key][price]' value='{$keySpecGoodsPrice[$item_key][price]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
            $str .="<td><input name='item[$item_key][store]' value='{$keySpecGoodsPrice[$item_key][store]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")'/><input type='hidden' name='item[$item_key][key_name]' value='$item_name' /></td>";  
            $str .="</tr>";           
       }
        $str .= "</table>";
       return $str;   
    }

    /**
     * 获取商品规格
     * @param $goods_id|商品id
     * @return array
     */
    public function get_spec($goods_id)
    {
        //商品规格 价钱 库存表 找出 所有 规格项id
        $keys = M('GoodsSpec')->where(['goodsId'=>$goods_id])->getField("GROUP_CONCAT(`key` SEPARATOR '_') ");
        $filter_spec = array();
        if ($keys) {
            // $specImage = M('SpecImage')->where(['goods_id'=>$goods_id,'src'=>['<>','']])->getField("spec_image_id,src");// 规格对应的 图片表， 例如颜色
            $keys = str_replace('_', ',', $keys);
            $sql = "SELECT a.name,a.sorts,b.* FROM __PREFIX__spec AS a INNER JOIN __PREFIX__spec_item AS b ON a.id = b.specId WHERE b.id IN($keys) ORDER BY b.id";
            $Model = new \Think\Model();
            $filter_spec2 = $Model->query($sql);
            $temp_keys = array();
            $tem = [];
            foreach ($filter_spec2 as $key => $val) {
                $tem[$val['name']][] = array(
                        'item_id' => $val['id'],
                        'item' => $val['item'],
                        // 'src' => $specImage[$val['id']],
                    );
                // $filter_spec[$val['name']][] = array(
                //     'item_id' => $val['id'],
                //     'item' => $val['item'],
                //     // 'src' => $specImage[$val['id']],
                // );
            }
            
            foreach ($tem as $k => $v) {
                if(!in_array($k, $temp_keys)){
                    $temp['specName'] = $k;
                    $temp['items'] = $v;
                    $filter_spec[] = $temp;
                    $temp_keys[] = $k;
                }
            }
        }
        // trace($filter_spec);
        unset($tem);
        unset($temp_keys);
        return $filter_spec;
    }
}