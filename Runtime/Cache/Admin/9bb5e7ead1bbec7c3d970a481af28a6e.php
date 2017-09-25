<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>唯公商城-管理主页</title>

    <meta name="keywords" content="唯公商城">
    <meta name="description" content="唯公商城">

    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->

    <!--<link rel="shortcut icon" href="favicon.ico">-->

    <link href="/Public/Admin/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/Public/Admin/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/Public/Admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/Public/Admin/css/animate.min.css" rel="stylesheet">
    <link href="/Public/Admin/css/style.min862f.css?v=4.1.0" rel="stylesheet">

    <script src="/Public/Admin/js/jquery.min.js?v=2.1.4"></script>
    <script src="/Public/Admin/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/Public/Admin/js/content.min.js?v=1.0.0"></script>

    <!-- jqGrid -->
    <link href="/Public/Admin/css/plugins/jqgrid/ui.jqgridffe4.css?0820" rel="stylesheet">
    <script src="/Public/Admin/js/plugins/jqgrid/i18n/grid.locale-cnffe4.js?0820"></script>
    <script src="/Public/Admin/js/plugins/jqgrid/jquery.jqGrid.minffe4.js?0820"></script>
    

    <script src="/Public/Admin/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/contabs.min.js"></script>
    <script src="/Public/Static/layer/layer.js"></script>

    
    <script src="/Public/Admin/js/check.js"></script>
    
</head>

    <body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">系统配置</a>
                            </li>
                            <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">支付配置</a>
                            </li>
                            <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false">物流配置</a>
                            </li>
                            <li class=""><a data-toggle="tab" href="#tab-4" aria-expanded="false">短信配置</a>
                            </li>
                        </ul>
                    </div>
                    <div class="ibox-content" style="border-top: 0;">
                        <div class="tab-content">
                            <div id="tab-1" class="tab-pane active">
                                <form role="form" action="<?php echo U('Config/edit');?>" id="form-add" class="form-horizontal">
                                    <table class="table table-bordered">
                                        <tbody>
                                        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                                                <td style="width:10%;">
                                                    <?php if($v['type'] == 'file'): echo ($v["name"]); ?><br><span class="text-warning">尺寸:200*200</span>
                                                        <?php else: ?>
                                                        <?php echo ($v["name"]); if(in_array(($v["config"]), explode(',',"reason,shopcate"))): ?><br/><span class="text-warning">(多个请用|分隔)</span><?php endif; endif; ?></td>
                                                <td>
                                                    <?php if($v['type'] == 'file'): ?><div id="<?php echo ($v["config"]); ?>"></div>
                                                        <?php else: ?>
                                                        <?php if(($v["type"]) == "src"): ?><img height="200" title="平台推广二维码" alt="平台推广二维码" src="http://qr.liantu.com/api.php?<?php echo ($v["value"]); ?>">
                                                        <?php else: ?>
                                                            <input type="<?php echo ($v["type"]); ?>" name="config[<?php echo ($v["config"]); ?>]" value="<?php echo ($v["value"]); ?>" placeholder="<?php echo ($v["name"]); ?>" class="form-control"><?php endif; endif; ?>
                                                </td>
                                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                        <tr>
                                            <td>操作</td>
                                            <td><button class="btn btn-primary" type="submit">保存</button></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                            <div id="tab-2" class="tab-pane">
                                <form role="form" action="<?php echo U('Config/editConfig');?>" id="form-edit" class="form-horizontal">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr><td colspan="2">微信支付配置：</td></tr>
                                            <tr>
                                                <td style="width:10%;">微信APPID</td>
                                                <td>
                                                    <input type="text" name="wxpay[app_id]" <?php if(!empty($wxpay["app_id"])): ?>value="<?php echo ($wxpay["app_id"]); ?>"<?php endif; ?> placeholder="请填写绑定支付的APPID" class="form-control">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:10%;">商户号MCHID</td>
                                                <td>
                                                    <input type="text" name="wxpay[mch_id]" <?php if(!empty($wxpay["mch_id"])): ?>value="<?php echo ($wxpay["mch_id"]); ?>"<?php endif; ?> placeholder="请填您的微信商户号" class="form-control">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:10%;">商户支付密钥KEY</td>
                                                <td>
                                                    <input type="text" name="wxpay[key]" <?php if(!empty($wxpay["key"])): ?>value="<?php echo ($wxpay["key"]); ?>"<?php endif; ?> placeholder="请填写商户支付密钥" class="form-control">
                                                </td>
                                            </tr>
                                            <tr><td colspan="2">支付宝支付配置：</td></tr>
                                            <tr>
                                                <td style="width:10%;">支付宝APPID</td>
                                                <td>
                                                    <input type="text" name="alipay[app_id]" <?php if(!empty($alipay["app_id"])): ?>value="<?php echo ($alipay["app_id"]); ?>"<?php endif; ?> placeholder="请填写应用ID,您的APPID" class="form-control">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:10%;">支付宝公钥</td>
                                                <td>
                                                    <textarea style="height: 150px;" name="alipay[public_key]" class="form-control"><?php if(!empty($alipay["public_key"])): echo ($alipay["public_key"]); endif; ?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:10%;">商户私钥</td>
                                                <td>
                                                    <textarea style="height: 250px;" name="alipay[private_key]" class="form-control"><?php if(!empty($alipay["private_key"])): echo ($alipay["private_key"]); endif; ?></textarea>
                                                </td>
                                            </tr>
                                            
                                        <tr>
                                            <td>操作</td>
                                            <td><button class="btn btn-primary" type="submit">保存</button></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                            <div id="tab-3" class="tab-pane">
                                <form role="form" action="<?php echo U('Config/editConfig');?>" id="form-edit1" class="form-horizontal">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr><td colspan="2">快递鸟物流配置：</td></tr>
                                            <tr>
                                                <td style="width:10%;">快递鸟EBusinessID</td>
                                                <td>
                                                    <input type="text" name="kdniao[biz_id]" <?php if(!empty($kdniao["biz_id"])): ?>value="<?php echo ($kdniao["biz_id"]); ?>"<?php endif; ?> placeholder="请填写您的EBusinessID" class="form-control">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:10%;">快递鸟AppKey</td>
                                                <td>
                                                    <input type="text" name="kdniao[app_key]" <?php if(!empty($kdniao["app_key"])): ?>value="<?php echo ($kdniao["app_key"]); ?>"<?php endif; ?> placeholder="请填您的AppKey" class="form-control">
                                                </td>
                                            </tr>
                                        <tr>
                                            <td>操作</td>
                                            <td><button class="btn btn-primary" type="submit">保存</button></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                            <div id="tab-4" class="tab-pane">
                                <form role="form" action="<?php echo U('Config/editConfig');?>" id="form-edit2" class="form-horizontal">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr><td colspan="2">短信账户配置：</td></tr>
                                            <tr>
                                                <td style="width:10%;">账户名</td>
                                                <td>
                                                    <input type="text" name="sms[account]" <?php if(!empty($sms["account"])): ?>value="<?php echo ($sms["account"]); ?>"<?php endif; ?> placeholder="请填写您的账户名" class="form-control">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:10%;">账户密码</td>
                                                <td>
                                                    <input type="text" name="sms[password]" <?php if(!empty($sms["password"])): ?>value="<?php echo ($sms["password"]); ?>"<?php endif; ?> placeholder="请填您的账户密码" class="form-control">
                                                </td>
                                            </tr>
                                        <tr>
                                            <td>操作</td>
                                            <td><button class="btn btn-primary" type="submit">保存</button></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </body>
    <link rel="stylesheet" href="/Public/Static/Huploadify/Huploadify.css">
    <script charset="utf-8" src="/Public/Static/Huploadify/jquery.Huploadify.js"></script>
    <script charset="utf-8" src="/Public/Static/Huploadify/Huploadify.js"></script>
    <script>
        $(function() {
            var upUrl = "<?php echo U('Admin/UploadFile/upimage');?>";
            var delUrl = "<?php echo U('Admin/UploadFile/delete');?>";
            //封面图片
            var images = '<?php echo ($images); ?>';
            images = JSON.parse(images);
            console.log(images);
            $.each(images,function(a,b){
                upimage(b.name, "config["+b.name+"]", upUrl, delUrl, b.value);
            });
            $(".uploadify-queue-item").css({"width":"100px"})
            $(".showthumb").css({"minHeight":"100px"});
        });
        $("#form-edit1,#form-edit2").submit(function(){
            var self = $(this);
            if($(document).find('#editor').length > 0){
                editor.sync(); 
            }
            $.post(self.attr("action"), self.serialize(), success, "json");
            
            return false;
            function success(data){
                if(data.status){
                    layer.msg(data.info, {
                        icon:1,
                        offset: 0,
                        shift: 0,
                        time:1500
                    },function(){
                        window.location.reload();//刷新当前页面 ;
                    });
                } else {
                    layer.msg(data.info, {
                        icon:0,
                        offset: 0,
                        shift: 6,
                        time:1500
                    }); 
                     
                }
            }
        });
    </script>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>