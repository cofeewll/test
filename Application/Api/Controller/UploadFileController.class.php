<?php
namespace Api\Controller;
use Common\Util\AuthUtil;
use Think\Controller;
use Common\Util\UploadUtil;
/**
 * [文件上传处理]
 */
class UploadFileController extends Controller
{
    public function upImage(){
        $img = UploadUtil::upimage();
        $data['pic'] = $img['url'];
        ajax_return_ok($data);
    }

   public function delete(){
       UploadUtil::delete();
    }
    /**
     * 修改图像
     */
    public function editPic(){
        //用户身份验证
        $result = AuthUtil::checkIdentity();
        if (!$result['status']){
            ajax_return_error(null,$result['code']);
        }
        $user=session(C("SESSION_NAME_CUR_HOME"));
        $uid=$user['id'];
        $img = UploadUtil::upimage();
        $data['img'] = $img['url'];
        $res=M("user")->where("id=$uid")->save($data);
        if($res){
            ajax_return_ok($data,"修改成功");
        }else{
            ajax_return_error("修改失败");
        }
    }
    
}