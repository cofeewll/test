<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/8 0008
 * Time: 下午 2:43
 */

namespace Api\Controller;


class SysController extends BaseController
{
    /**
     * 获取系统文章
     */
    public function getArticle(){
        $id=I("id");
        $data=M("article")->where("id=$id")->field("title,context")->find();
        $content=str_replace('src="/Uploads','src="http://'.$_SERVER['HTTP_HOST'].'/Uploads',$data['context']);
        $data['context']=htmlspecialchars_decode($content);
        ajax_return_ok($data,"请求成功");
    }
    /**
     * 帮助文章
     */
    public function help(){
        $data=M("article")->where("type=1")->order("id desc")->select();
        foreach($data as $k=>$v){
            $content=str_replace('src="/Uploads','src="http://'.$_SERVER['HTTP_HOST'].'/Uploads',$v['context']);
            $data[$k]['context']=htmlspecialchars_decode($content);
        }
        ajax_return_ok($data,"请求成功");
    }
    /**
     * 获取店铺分类
     */
    public function cate(){
        $cate=M("config")->where("id=10")->getField("value");
        ajax_return_ok(explode("|",trim($cate,"|")),"请求成功");
    }
}