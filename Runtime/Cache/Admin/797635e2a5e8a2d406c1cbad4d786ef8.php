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
    
<link rel="stylesheet" href="/Public/Static/kindeditor/themes/simple/simple.css" />
<script type="text/javascript" charset="utf-8" src="/Public/Static/kindeditor/kindeditor.js"></script>
<script type="text/javascript" charset="utf-8" src="/Public/Static/kindeditor/lang/zh_CN.js"></script>
 

</head>

    <body class="gray-bg">
    <div class="wrapper wrapper-content  animated fadeInRight">
        <div class="row">
            <div class="col-sm-12 ibox float-e-margins">
                <div class="tabs-container">
                    <div class="ibox-title" style="border-bottom: 0;border-top: 0;">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">基本信息</a>
                            </li>
                            <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">店铺信息</a>
                            </li>
                            <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false">图文介绍</a>
                            </li>
                            <li class=""><a data-toggle="tab" href="#tab-4" aria-expanded="false">认证信息</a>
                            </li>
                        </ul>
                    </div>
                    <div class="ibox-content" style="border-top: 0;">
                        <form role="form" action="<?php echo U('Shop/add');?>" id="form-add" class="form-horizontal">
                            <input type="hidden" <?php if(!empty($info)): ?>value="<?php echo ($info['id']); ?>"<?php endif; ?> name="id" />  
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane active">
                                    
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >商家账号</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" class="form-control" <?php if(!empty($info)): ?>readonly value="<?php echo ($info['username']); ?>"<?php endif; ?> name="username" style="width:60%;display:inline-block;" autocomplete="off" placeholder="商家账号">
                                            <label style="color: red;font-size:20px;" >&nbsp;*</label>
                                        </div>
                                        
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >登录密码</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" class="form-control" name="password" style="width:60%;" autocomplete="off" <?php if(!empty($info)): ?>placeholder="不修改为空"<?php else: ?>placeholder="登录密码"<?php endif; ?>>
                                        </div>
                                        
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >商家名称</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['title']); ?>"<?php endif; ?> name="title" style="width:60%;display:inline-block;" autocomplete="off" placeholder="商家名称">
                                            <label style="color: red;font-size:20px;" >&nbsp;*</label>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >真实姓名</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['realname']); ?>"<?php else: ?>value=""<?php endif; ?> style="width:60%;">
                                        </div>
                                        
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >手机号码</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['phone']); ?>"<?php endif; ?> name="phone"  style="width:60%;display:inline-block;"  autocomplete="off" placeholder="手机号码" style="width:250px;" onkeyup="this.value=this.value.replace(/\D/g,'')">
                                            <label style="color: red;font-size:20px;" >&nbsp;*</label>
                                        </div>
                                        
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >手续费比例</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" name="shopFee" class="form-control" <?php if(!empty($info)): if(($info["shopFee"]) == "-1"): ?>value="未设置"<?php else: ?>value="<?php echo ($info['shopFee']); ?>"<?php endif; else: ?>value="0.1"<?php endif; ?> style="width:60%;display:inline-block;">
                                            <label style="color: red;font-size:12px;" >&nbsp;（请填写0-1之间小数）</label>
                                        </div>
                                        
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <!-- <div class="form-group">
                                        <label for="account" class="col-sm-2 control-label" >账户余额</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" readonly class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['account']); ?>"<?php else: ?>value="0.00"<?php endif; ?> style="width:60%;">
                                        </div>
                                        
                                    </div>
                                    <div class="hr-line-dashed"></div>   -->
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">状态</label>
                                        <div class="col-sm-10 shot">
                                            <label class="i-checks">
                                                <input type="radio" value="1" name="status" <?php if(!empty($info)): if($info['status'] == 1): ?>checked<?php endif; else: ?>checked<?php endif; ?> > <i></i> 正常&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                            <label class="i-checks">
                                                <input type="radio" value="0" name="status" <?php if(!empty($info)): if($info['status'] == 0): ?>checked<?php endif; endif; ?>> <i></i> 禁用&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                           <!--  <label class="i-checks">
                                                <input type="radio" value="-1" name="status" <?php if(!empty($info)): if($info['status'] == -1): ?>checked<?php endif; endif; ?>> <i></i> 待审核&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                            <label class="i-checks">
                                                <input type="radio" value="-2" name="status" <?php if(!empty($info)): if($info['status'] == -2): ?>checked<?php endif; endif; ?>> <i></i> 拒绝通过&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label> -->
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2">&nbsp;</label>
                                        <button class="btn btn-primary" type="submit">保存</button>
                                    </div>
                            
                                </div>
                                <div id="tab-2" class="tab-pane">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >商家地址</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['address']); ?>"<?php endif; ?> name="address" style="width:60%;" autocomplete="off" placeholder="商家地址">
                                        </div>
                                        
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >商家电话</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['tel']); ?>"<?php endif; ?> name="tel" style="width:60%;" autocomplete="off" placeholder="商家电话">
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >客服QQ</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['qq']); ?>"<?php endif; ?> name="qq" style="width:60%;" autocomplete="off" placeholder="客服QQ">
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >商家logo</label>
                                        <div class="col-sm-10 shot">
                                            <div>(支持:jpg、jpeg、png、gif； 尺寸：宽160*高90px；大小：限5M内)</div>
                                            <div id="img"></div>
                                        </div>
                                        
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >相册</label>
                                        <div class="col-sm-10 shot">
                                            <div>(支持:jpg、jpeg、png、gif； 尺寸：宽320*高230px；大小：限5M内)</div>
                                            <div id="images"></div>
                                        </div>
                                        
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2">&nbsp;</label>
                                        <button class="btn btn-primary" type="submit">保存</button>
                                    </div>
                            
                                </div>
                                <div id="tab-3" class="tab-pane">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >商家简介</label>
                                        <div class="col-sm-10 shot">
                                            <textarea class="form-control" name="description" style="width: 80%;height: 80px;"><?php if(!empty($info)): echo ($info['description']); endif; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >图文介绍</label>
                                        <div class="col-sm-10 shot">
                                            <textarea class="form-control" name="detail" style="width: 80%;height: 300px;" id="editor"><?php if(!empty($info)): echo ($info['detail']); endif; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2">&nbsp;</label>
                                        <button class="btn btn-primary" type="submit">保存</button>
                                    </div>
                                </div>
                                <div id="tab-4" class="tab-pane">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >营业执照</label>
                                        <div class="col-sm-10 shot">
                                            <div id="license"></div>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >资质证书</label>
                                        <div class="col-sm-10 shot">
                                            <div id="certify"></div>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2">&nbsp;</label>
                                        <button class="btn btn-primary" type="submit">保存</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
    <script type="text/javascript">
        var editor;
        KindEditor.ready(function(K) {
            editor = K.create('#editor', {
                themeType : 'simple',
                resizeType: 1,
            });
        });
    </script>
    <link rel="stylesheet" href="/Public/Static/Huploadify/Huploadify.css">
    <script charset="utf-8" src="/Public/Static/Huploadify/jquery.Huploadify.js"></script>
    <script charset="utf-8" src="/Public/Static/Huploadify/Huploadify.js"></script>
    <script>
        $(function() {
            var upUrl = "<?php echo U('Admin/UploadFile/upimage');?>";
            var delUrl = "<?php echo U('Admin/UploadFile/delete');?>";
            //封面图片
            var img = "<?php echo ($info['img']); ?>";
            var license = "<?php echo ($info['license']); ?>";
            var certify = "<?php echo ($info['certify']); ?>";
            var images = "<?php echo ($info['images']); ?>";
            upimage("img", "img", upUrl, delUrl, img);
            upimage("license", "license", upUrl, delUrl, license);
            upimage("certify", "certify", upUrl, delUrl, certify);
            upimages("images", "images", upUrl, delUrl, images);
        });
    </script>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>