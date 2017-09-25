<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    
    <title>咭咭生活-添加校区</title>


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

    <style>
        .shot{width:60%}
    </style>
    <body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>编辑公告</h5>
                    </div>
                    <div class="ibox-content">
                        <form method="post" class="form-horizontal" action="<?php echo U('edit');?>">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">标题名称</label>
                                <div class="col-sm-10 shot">
                                    <input type="text" class="form-control" name="title" value="<?php echo ($data["title"]); ?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">查看角色</label>
                                <div class="col-sm-10 shot">
                                    <input type="radio"  name="type" value="0" <?php if($data['type']==0)echo "checked"; ?> >全部
                                    <input type="radio"  name="type" value="1" <?php if($data['type']==1)echo "checked"; ?> >用户
                                    <input type="radio"  name="type" value="2" <?php if($data['type']==2)echo "checked"; ?> >商家
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">内容</label>

                                <div class="col-sm-10 shot">
                                    <div class="input-group">
                                        <textarea class="form-control" style="height:300px;" id="content"  name="content"><?php echo ($data["content"]); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <input type="hidden" name="id" value="<?php echo ($data["id"]); ?>">
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <button class="btn btn-white" type="reset">重置</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
    <link rel="stylesheet" href="/Public/Static/kindeditor/themes/simple/simple.css" />
    <script type="text/javascript" charset="utf-8" src="/Public/Static/kindeditor/kindeditor-min.js"></script>
    <script type="text/javascript" charset="utf-8" src="/Public/Static/kindeditor/lang/zh_CN.js"></script>
    <script>
        var editor;
        KindEditor.ready(function(K) {
            editor = K.create('#content', {
                themeType : 'simple',
                resizeType: 1,
            });
        });
        //表单提交
        $(document)
                .ajaxStart(function(){
                    $("button:submit").addClass("btn-default").removeClass('btn-primary').attr("disabled", true);
                })
                .ajaxStop(function(){
                    $("button:submit").removeClass("btn-default").addClass("btn-primary").attr("disabled", false);
                });
        $("form").submit(function(){
            editor.sync();
            $.post($(this).attr("action"),$(this).serialize(),function(data){
                if(data.status){
                    layer.msg(data.msg, {
                        icon:1,
                        offset: 0,
                        shift: 0,
                        time:1500
                    },function(){
                        window.location.reload();//刷新当前页面 ;
                    });
                } else {
                    layer.msg(data.msg, {
                        icon:0,
                        offset: 0,
                        shift: 6,
                        time:1500
                    });

                }
            })
            return false;
        })
    </script>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>