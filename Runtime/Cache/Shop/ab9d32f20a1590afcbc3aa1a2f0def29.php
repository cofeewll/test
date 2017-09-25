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
    <div class="wrapper wrapper-content">
        <div class="row animated fadeInRight">
            <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>评论展示</h5>
                </div>
                <div class="ibox-content">
                    <div>
                        <div class="feed-activity-list">

                            <div class="feed-element">
                                <a class="pull-left">
                                    <img alt="image" class="img-circle" src="<?php echo ($user["img"]); ?>">
                                </a>
                                <div class="media-body ">
                                    <small class="pull-right text-navy"><?php echo (date("Y-m-d H:i:s",$data["createTime"])); ?></small>
                                    <strong><?php echo ($user["nickname"]); ?></strong> 评论了 <strong><?php echo ($goods["name"]); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo ($data["spec_key_name"]); ?></small>
                                    <div class="well">
                                        <?php echo ($data["context"]); ?>
                                    </div>
                                    <div class="photos">
                                        <?php if(is_array($data["imgs"])): $i = 0; $__LIST__ = $data["imgs"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><img alt="image" class="feed-photo" src="<?php echo ($v); ?>"><?php endforeach; endif; else: echo "" ;endif; ?>

                                    </div>
                                </div>
                            </div>

                    </div>

                </div>
            </div>
                <div class="ibox-content">
                    <table class="table table-bordered">
                        <tr><td>回复他/她</td><td><textarea class="form-control" id="reply"><?php echo ($data["reply"]); ?></textarea></td></tr>
                        <?php if($data['replyTime'] > 0): ?><tr><td>回复时间</td><td><?php echo (date("Y-m-d H:i:s",$data["replyTime"])); ?></td></tr>
                            <?php else: ?>
                            <tr><td>操作</td><td><button class="btn-sm btn-primary" attr="<?php echo ($data["id"]); ?>">保存</button></td></tr><?php endif; ?>

                    </table>
                </div>

        </div>
                </div>
            </div>
        </div>
    </body>
<script>
    $("button").click(function(){
        var content=$("#reply").val();
        if(content==''){
            layer.msg("请输入回复内容");return ;
        }
        $.post("<?php echo U('reply');?>",{reply:content,id:$(this).attr("attr")},function(data){
            layer.msg(data.msg);location.replace(location.href);
        })
    })
</script>
    

<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>