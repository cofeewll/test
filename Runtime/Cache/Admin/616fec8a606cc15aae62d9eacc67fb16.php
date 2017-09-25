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
    
<script charset="utf-8" src="/Public/Static/laydate/laydate.js"></script>
<link rel="stylesheet" href="/Public/Static/kindeditor/themes/simple/simple.css" />
<script type="text/javascript" charset="utf-8" src="/Public/Static/kindeditor/kindeditor-min.js"></script>
<script type="text/javascript" charset="utf-8" src="/Public/Static/kindeditor/lang/zh_CN.js"></script>
 <style>
    .hond {
        overflow: hidden;
    }
    .hond>* {
        float: left;
        line-height: 34px;
    }
    .control-length{
        width: 100px;
        text-align: right;
    }
    .form-group{
        margin-bottom:5px;
    }
    </style>

</head>

    <body class="gray-bg">
   <div class="wrapper wrapper-content  animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
            <form role="form" action="<?php echo U('Article/addSave');?>" id="form-add">
                <div class="form-group hond">
                    <label class="control-length">标题：</label>
                    <input type="text" name="aTitle" placeholder="标题" style="width:60%;" class="form-control">
                    <label style="color: red;font-size:20px;" >&nbsp;*</label>
                </div>
                <div class="form-group hond">
                    <label class="control-length">文章类型：</label>
                    <select class="form-control" name="aType" style="width:60%;" >
                        <option value="1">帮助中心</option>
                        <option value="2">其它</option>
                    </select>
                </div>

                <div class="form-group  hond">
                    <label class="control-length">排序：</label>
                    <input type="text" class="form-control" name="aSort" value="100" style="width:60%;" autocomplete="off" >
                </div>

                <div class="form-group  hond">
                        <label class="control-length">内容：</label>
                        <textarea class="form-control" style="width:80%;height:300px;" id="aContext"  name="aContext"></textarea>
                </div>

                <div class="form-group">
                    <label class="control-length">&nbsp;</label>
                    <button class="btn btn-primary" type="submit">保存</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    </body>
<script>
$(document)
    .ajaxStart(function() {
        $("button:submit").addClass("btn-default").removeClass('btn-primary').attr("disabled", true);
    })
    .ajaxStop(function() {
        $("button:submit").removeClass("btn-default").addClass("btn-primary").attr("disabled", false);
    });

$("form").submit(function() {
    editor.sync();
    var self = $(this);    
    $.post(self.attr("action"), self.serialize(), success, "json");
    return false;

    function success(data) {
        if (data.status) {
            layer.msg(data.info, {
                icon: 1,
                offset: 0,
                shift: 0,
                time: 1500
            }, function() {
                window.location.reload(); //刷新当前页面 ;
            });
        } else {
            layer.msg(data.info, {
                icon: 0,
                offset: 0,
                shift: 6,
                time: 1500
            });
        }
    }
});


var editor;
KindEditor.ready(function(K) {
    editor = K.create('#aContext', {
        themeType : 'simple',
        resizeType: 1,
    });
});

</script>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>