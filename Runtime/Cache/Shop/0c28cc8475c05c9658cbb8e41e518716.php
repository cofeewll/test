<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>唯公商城-商家管理主页</title>

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

    <link href="/Public/Admin/css/plugins/chosen/chosen.css" rel="stylesheet">

    <body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>账号信息</h5>
                    </div>
                    <div class="ibox-content">
                        <form method="post">
                            <table class="table table-bordered">

                                <tbody>

                                <tr>
                                    <td>微信号</td><td><input type="text" class="form-control" value="<?php echo ($data["wechat"]); ?>" name="wechat"></td></tr>
                                <tr><td>支付宝号</td><td><input type="text" class="form-control" value="<?php echo ($data["alipay"]); ?>" name="alipay"></td></tr>
                                <tr>
                                    <td>银行卡号&nbsp;&nbsp;<span class="btn-xs  btn-primary  m-t-n-xs"  onclick="add(this)"><i class="fa fa-plus" ></i></span></td>
                                    <td>
                                        <?php if(is_array($data["bank"])): $i = 0; $__LIST__ = $data["bank"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><input type="text" class="form-control" value="<?php echo ($v); ?>" name="bank[]" placeholder="请输入银行卡号"><br><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </td></tr>
                                <tr>
                                    <td>操作</td>
                                    <td>
                                        <input type="hidden" name="id" value="<?php echo ($data['id']); ?>">
                                        <button class="btn-xs btn-primary">编辑</button></td>
                                </tr>

                                </tbody>

                            </table>
                        </form>
                    </div>
                </div>
            </div>



        </div>
    </div>


    </body>

    <script>
        function add(v){
            $(v).parent().next("td").append('<input type="text" placeholder="请输入银行卡号" class="form-control" name="bank[]"><br>');
        }
        $("form").submit(function(){
            $.post("<?php echo U('setBank');?>",$("form").serialize(),function(ret){
                layer.msg(ret.msg);
            });
            return false;
        })
    </script>



<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>