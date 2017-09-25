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
                        <h5>申请提现</h5>
                    </div>
                    <div class="ibox-content">
                        <form method="post">
                            <table class="table table-bordered">

                                <tbody>

                                <tr><td>手续费比例</td><td><input type="text" class="form-control" value="<?php echo ($data["shopFee"]); ?>" readonly name="fee"></td></tr>
                                <tr><td>可提现金额</td><td><input type="text" class="form-control" value="<?php echo ($money); ?>" readonly></td></tr>
                                <tr><td>请输入提现金额</td><td><input type="text" class="form-control"  name="amount"></td></tr>
                                <tr>
                                    <td>选择提现账户</td>
                                    <td>
                                        <select class="form-control" name="type" onchange="select_account(this)">
                                            <option value="0">请选择提现账户</option>
                                            <option value="1">支付宝</option>
                                            <option value="2">微信</option>
                                            <option value="3">银行卡</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>操作</td>
                                    <td>
                                        <input type="hidden" name="id" value="<?php echo ($data['id']); ?>">
                                        <button class="btn-sm btn-primary">编辑</button></td>
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
        function select_account(v){
            var value=$(v).val();
            $("#account").remove();
            if(value!=0){
                $.post("getAccount",{type:value},function(data){
                    if(value<3){
                        var html="<tr id='account'><td>账号</td><td><input type='text' name='account' readonly value='"+data.data+"' class='form-control'></td></tr>";
                        $(v).parent().parent().after(html);
                    }else{
                        var html="<tr id='account'><td>账号</td><td><select name='account' class='form-control'>";
                        $.each(data.data,function(a,b){
                            html+="<option value='"+b+"'>"+b+"</option>";
                        })
                        html+="</select></td></tr>";
                        $(v).parent().parent().after(html);
                    }
                })
            }
        }
        $("form").submit(function(){
            $.post("<?php echo U('apply');?>",$("form").serialize(),function(ret){
                layer.msg(ret.msg);
            });
            return false;
        })
    </script>



<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>