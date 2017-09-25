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
                    <div class="ibox-content">
                        <form role="form" action="<?php echo U('Wheel/editRecord');?>" id="form-add1" class="form-horizontal">
                        <input type="hidden" name="fid" value="<?php echo ($fid); ?>" />
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td style="width: 40%;">中奖人</td>
                                <td>奖品</td>
                            </tr>
                            <?php if(!empty($children)): if(is_array($children)): foreach($children as $key=>$vo): ?><tr>
                                        <td>
                                            <input name="user[]" type="hidden" value="<?php echo ($vo["uid"]); ?>">
                                            <?php if(($vo["uid"]) == $info["uid"]): ?>中奖人：<?php endif; ?>
                                            [编号:<?php echo ($vo["number"]); ?>]&nbsp;<?php echo ($vo["phone"]); ?>
                                        </td>
                                        <td>
                                            <select name="award[]" class="form-control">
                                                <option value="">请选择</option>
                                                <?php if(is_array($prize)): $i = 0; $__LIST__ = $prize;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if($vo["pid"] == $key): ?>selected<?php endif; ?>><?php echo ($val); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                            </select>
                                        </td>
                                    </tr><?php endforeach; endif; ?>
                            <?php else: ?>
                                 <tr>
                                    <td>
                                        <input name="user[]" type="hidden" value="<?php echo ($info["uid"]); ?>">
                                        中奖人：[编号:<?php echo ($info["number"]); ?>]&nbsp;<?php echo ($info["phone"]); ?>
                                    </td>
                                    <td>
                                        <select name="award[]" class="form-control">
                                            <option value="">请选择</option>
                                            <?php if(is_array($prize)): $i = 0; $__LIST__ = $prize;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($val); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </td>
                                </tr>
                                <?php if(is_array($parents)): $i = 0; $__LIST__ = $parents;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                        <td>
                                            <input name="user[]" type="hidden" value="<?php echo ($vo["id"]); ?>">
                                            [编号:<?php echo ($vo["number"]); ?>]&nbsp;<?php echo ($vo["phone"]); ?>
                                        </td>
                                        <td>
                                            <select name="award[]" class="form-control">
                                                <option value="">请选择</option>
                                                <?php if(is_array($prize)): $i = 0; $__LIST__ = $prize;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($val); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                            </select>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                            <?php if(empty($children)): ?><tr>
                                    <td>操作</td>
                                    <td><button class="btn btn-primary" type="submit">保存</button></td>
                                </tr><?php endif; ?>
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
        
        $("#form-add1").submit(function(){
            var flag = true;
            $("select[name='award[]']").each(function(){
                if( $(this).val() == '' ){
                    layer.msg('还有用户没有派奖');
                    flag = false;
                    return false;
                }
            });
            if(!flag){
                return flag;
            }

            var self = $(this);
            
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