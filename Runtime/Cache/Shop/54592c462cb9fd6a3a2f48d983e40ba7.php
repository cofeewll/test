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

    <body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>退款商品信息</h5>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <td>商品图片</td>
                                <td>商品名称</td>
                                <td>商品规格</td>
                                <td>商品数量</td>
                                <td>商品价格</td>
                            </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td><img style="width:80px;height:80px;" src="<?php echo ($goods["img"]); ?>"></td>
                                    <td><?php echo ($goods["name"]); ?></td>
                                    <td><?php echo ($goods["spec"]); ?></td>
                                    <td><?php echo ($goods["number"]); ?></td>
                                    <td><?php echo ($goods["price"]); ?></td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>退款信息</h5>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td>是否已收货</td>
                                <td>
                                    <?php if($refund['type'] == 0): ?><input type="radio" checked>未收货
                                        <input type="radio" >已收货
                                        <?php else: ?>
                                        <input type="radio">未收货
                                        <input type="radio" checked>已收货<?php endif; ?>
                                </td>
                            </tr>
                            <tr><td>申请退款金额</td><td><?php echo ($refund["amount"]); ?></td></tr>
                            <tr><td>退款原因</td><td><?php echo ($refund["reason"]); ?></td></tr>
                            <tr><td>退款文字说明</td><td><?php echo ($refund["remark"]); ?></td></tr>
                            <tr>
                                <td>图片说明</td>
                                <td>
                                    <img style="width:80px;height:80px;" src="<?php echo ($goods["img"]); ?>">
                                </td>
                            </tr>
                            <!--<tr><td>运费</td><td><?php echo ($order["fee"]); ?></td></tr>-->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>处理信息</h5>
                    </div>
                    <div class="ibox-content">
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo ($refund["id"]); ?>">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>处理申请</td>
                                    <td>
                                        <?php if($refund['status'] == 1): ?><input type="radio" name="status" value="1" checked>同意退款
                                            <input type="radio" name="status" value="2">拒绝退款
                                            <?php else: ?>
                                            <input type="radio" name="status" value="1" >同意退款
                                            <input type="radio" name="status" value="2" checked>拒绝退款<?php endif; ?>

                                    </td>
                                </tr>
                                <tr><td>实际退款金额</td><td><input type="text" class="form-control" name="realMoney" value="<?php echo ($refund["realMoney"]); ?>"></td></tr>
                                <tr><td>补偿金额</td><td><input type="text" class="form-control" name="addMoney" value="<?php echo ($refund["addMoney"]); ?>"></td></tr>
                                <tr><td>备注</td><td><textarea name="dealContext" class="form-control"><?php echo ($refund["dealContext"]); ?></textarea></td></tr>
                                <?php if($refund['dealTime'] > 0): ?><tr><td>处理时间</td><td><?php echo (date("Y-m-d H:i:s",$refund["dealTime"])); ?></td></tr>
                                    <?php else: ?>
                                    <tr><td>操作</td><td><button class="btn-sm btn-primary">保存</button></td></tr><?php endif; ?>

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
        $("form").submit(function(){
            $.post("<?php echo U('doRefund');?>",$(this).serialize(),function(data){
                layer.msg(data.msg);
            })
            return false;
        })
    </script>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>