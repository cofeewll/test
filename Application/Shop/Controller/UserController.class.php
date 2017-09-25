<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5 0005
 * Time: 上午 11:17
 */

namespace Shop\Controller;


class UserController extends BaseController
{
    public function index(){
        $uid=$this->sid;
        $data=M("shop")->where("id=$uid")->find();
        $this->data=$data;
        $this->picture=M("picture")->where("sid=$uid and type=2")->select();
        $this->banner=M("picture")->where("sid=$uid and type=3")->select();
        $this->display();
    }
    /**
     * 处理相册
     */
    public function picture(){
        $type=I("post.type");
        $uid=$this->sid;
        switch ($type){
            case "add":
                $map['sid']=$uid;
                $map['type']=2;
                $map['path']=I("post.picture");
                $res=M("picture")->add($map);
                if($res){
                    ajax_return_ok([],"添加成功");
                }else{
                    ajax_return_error("添加失败");
                }
                break;
            case "edit":
                $id=I("post.id");
                $path=M("picture")->where("id=$id")->getField("path");
                unlink($path);
                $res=M("picture")->where("id=$id")->setField("path",I("post.picture"));
                if($res!==false){
                    ajax_return_ok([],"编辑成功");
                }else{
                    ajax_return_error("编辑失败");
                }
                break;
            case "del":
                $id=I("post.id");
                $path=M("picture")->where("id=$id")->getField("path");
                unlink($path);
                $res=M("picture")->delete($id);
                if($res){
                    ajax_return_ok([],"删除成功");
                }else{
                    ajax_return_error("删除失败");
                }
                break;
        }
    }
    /**
     * 处理轮播图
     */
    public function banner(){
        $type=I("post.type");
        $uid=$this->sid;
        switch ($type){
            case "add":
                $map['sid']=$uid;
                $map['type']=3;
                $map['path']=I("post.picture");
                $res=M("picture")->add($map);
                if($res){
                    ajax_return_ok([],"添加成功");
                }else{
                    ajax_return_error("添加失败");
                }
                break;

        }
    }
    public function editShop(){
        $id=$this->sid;
        $post=I("post.");
//        foreach($post as $v){
//            if(empty($v)){
//                ajax_return_error("请将信息填写完整");
//            }
//        }
        $res=M("shop")->where("id=$id")->save($post);
        if($res!==false){
            ajax_return_ok([],"保存成功");
        }else{
            ajax_return_error("保存失败");
        }
    }
    public function setProvinces(){
        $str=trim(I("post.str","","trim"),"|");
        $id=$this->sid;
        $res=M("shop")->where("id=$id")->setField("provinces",$str);
        if($res!==false){
            ajax_return_ok([],"设置成功");
        }else{
            ajax_return_error("设置失败");
        }
    }
}