<?php
/**
 * Created by PhpStorm.
 * User: hzd
 * Date: 2017/6/28
 * Time: 10:13
 */

namespace Admin\Model;
use Think\Model;

class BaseModel extends Model
{
    /**
     * [getIp 获取ip]
     * @return [string] [ip地址]
     */
    protected function getIp(){
        return get_client_ip();
    }
    /**
     * [checkPhone 检测手机号格式]
     * @return [type] [description]
     */
    protected function checkPhone(){
        if(isset($_POST['mobile']) && !check_mobile($_POST['mobile'])) {
            return false;
        }
        if(isset($_POST['phone']) && !check_mobile($_POST['phone'])) {
            return false;
        }
        return true;
    }
    /**
     * [checkEmail 检测邮箱格式]
     * @return [type] [description]
     */
    protected function checkEmail(){
        if( !check_email($_POST['email'])) {
            return false;
        }
        return true;
    }
    
    /**
     * [checkPwd 检测添加用户时密码是否为空]
     * @return [type] [description]
     */
    protected function checkPwd(){
        $id = intval($_POST['id']);
        $pwd = trim($_POST['password']);
        if(!$id && empty($pwd)){
            return false;
        }
        return true;
    }
    /**
     * [getAdminPwd 获取管理员密码加密字段]
     * @return [type] [description]
     */
    protected function getAdminPwd(){
        if($_POST['password']){
            return think_md5($_POST['password'], C('UC_AUTH_KEY'));
        }else{
            return '';
        }
    }
    /**
     * [getPwd 获取用户密码加密字段]
     * @return [type] [description]
     */
    protected function getPwd(){
        if($_POST['password']){
            return encrypt_pass($_POST['password']);
        }else{
            return '';
        }
    }

    /**
     * 新增或更新
     */
    public function update(){
        $data = $this->create();
        if(!$data){ //数据对象创建错误
            return false;
        }
        /* 添加或更新数据 */
        if(empty($data['id'])){
            $res = $this->add();
            if(!$res){
                $this->error = '操作失败';
                return false;
            }
        }else{
            if(isset($data['password']) && empty(trim($data['password']))){
                unset($data['password']);
            }
            $res = $this->save($data);
            if($res === false){
                $this->error = '操作失败';
                return false;
            }else{
                $res = true;
            }
        }
        return $res;
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

    /**
     * 修改数据记录排序
     */
    public function changeSort(){
        $id = intval($_POST['id']);
        $value = intval($_POST['value']);
        if(!$id){
            $this->error = '缺失参数';
            return false;
        }
        if (!is_numeric($value)) {
            $this->error = '更新失败，排序只能填写数字';
        }
        $data = array();
        $data['id'] = $id;
        $data['sorts'] = $value;
        $optRes = $this->save($data);
        if($optRes === false){
            $this->error = '操作失败';
            return false;
        }
        return true;
    }

    /**
     * 根据主键删除一条记录
     */
    public function delRec(){
        $id = intval($_POST['id']);
        if(!$id){
            $this->error = '缺失参数';
            return false;
        }
        $rec = $this->where(array('id'=>$id))->find();
        if(empty($rec)){
            $this->error = '信息不存在';
            return false;
        }
        $optRes = $this->delete($id);
        if(!$optRes){
            $this->error = '操作失败';
            return false;
        }
        return true;
    }

    /**
     * [upImages 处理商家、商品上传相册图片]
     * @return [type] [description]
     */
    public function upImages($images,$sid,$type=1){
        if(is_string($images)){
            $images = [$images];
        }
        $model = M('Picture');
        $temp = $model->where(['sid'=>$sid,'type'=>$type])->getField('path',true);
        $temp = is_array($temp)?$temp:[];
        // trace('====images====');
        // trace($images);
        // trace('====temp====');
        // trace($temp);
        $delArr = array_diff($temp,$images);
        $addArr = array_diff($images,$temp);
        // trace('====delArr====');
        // trace($delArr);
        // trace('====addArr====');
        // trace($addArr);
        if(!empty($addArr)){
            foreach ($addArr as $k => $v) {
                $tem['sid'] = $sid;
                $tem['path'] = $v;
                $tem['type'] = $type;
                $addData[] = $tem;
            }
            $addRes = $model->addAll($addData);
            if(!$addRes){
                return false;
            }
        }
        if(!empty($delArr)){
            $delRes = $model->where(['path'=>['in',$delArr]])->delete();
            if(!$delRes){
                return false;
            }
        }
        return true;
    }
}