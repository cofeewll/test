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
    <div class="wrapper wrapper-content  animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form role="form" action="<?php echo U('Wheel/addPrize');?>" id="form-add" class="form-horizontal">
                            <input type="hidden" <?php if(!empty($info)): ?>value="<?php echo ($info['id']); ?>"<?php endif; ?> name="id" />
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >奖品名称</label>
                                <div class="col-sm-10 shot">
                                    <input type="text" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['name']); ?>"<?php endif; ?> name="name" style="width:60%;" autocomplete="off" placeholder="奖品名称">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">奖品分类</label>
                                <div class="col-sm-10 shot">
                                    <select class="form-control" id="cate" name="cate" style="width:60%;">
                                        <option value="">选择分类</option>
                                            <option value="1" <?php if(!empty($info)): if(($info["cate"]) == "1"): ?>selected<?php endif; endif; ?> >抽奖奖品</option>
                                            <option value="2" <?php if(!empty($info)): if(($info["cate"]) == "2"): ?>selected<?php endif; endif; ?> >派奖奖品</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div id="typeDiv" <?php if(!empty($info)): if(($info["cate"]) == "2"): ?>style="display:none;"<?php else: ?>style="display: block;"<?php endif; else: ?>style="display: block;"<?php endif; ?>>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">奖品类型</label>
                                    <div class="col-sm-10 shot">
                                        <select class="form-control" name="type" style="width:60%;" id="type">
                                            <option value="">选择分类</option>
                                            <option value="1" <?php if(!empty($info)): if(($info["type"]) == "1"): ?>selected<?php endif; endif; ?> >特殊奖</option>
                                            <option value="2" <?php if(!empty($info)): if(($info["type"]) == "2"): ?>selected<?php endif; endif; ?> >金币</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>
                            <div id="goldDiv" <?php if(!empty($info)): if(($info["type"]) == "2"): ?>style="display:block;"<?php else: ?>style="display: none;"<?php endif; else: ?>style="display: none;"<?php endif; ?>>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" >金币数额</label>
                                    <div class="col-sm-10 shot">
                                        <input type="number" min="0" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['amount']); ?>"<?php endif; ?> name="amount"  style="width:60%;"  autocomplete="off" placeholder="奖励金币数额" style="width:250px;">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>
                            <!-- <div class="form-group">
                                <label class="col-sm-2 control-label" >奖品描述</label>
                                <div class="col-sm-10 shot">
                                    <input type="text" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['description']); ?>"<?php endif; ?> name="description"  style="width:60%;"  autocomplete="off" placeholder="奖品描述" style="width:250px;">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div> -->
                            <!-- <div class="form-group">
                                <label class="col-sm-2 control-label" >奖品图片</label>
                                <div class="col-sm-10 shot">
                                    <div>(支持:jpg、jpeg、png、gif； 尺寸：宽200*高200px；大小：限5M内)</div>
                                    <div id="img"></div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div> -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >排序</label>
                                <div class="col-sm-10 shot">
                                    <input type="number" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['sorts']); ?>"<?php else: ?>value="50"<?php endif; ?> name="sorts"  style="width:60%;"  autocomplete="off" style="width:250px;">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态</label>
                                <div class="col-sm-10 shot">
                                    <label class="i-checks">
                                        <input type="radio" value="1" name="status" <?php if(!empty($info)): if($info['status'] == 1): ?>checked<?php endif; else: ?>checked<?php endif; ?> > <i></i> 正常&nbsp;&nbsp;&nbsp;&nbsp;
                                    </label>
                                    <label class="i-checks">
                                        <input type="radio" value="0" name="status" <?php if(!empty($info)): if($info['status'] == 0): ?>checked<?php endif; endif; ?> > <i></i> 禁用&nbsp;&nbsp;&nbsp;&nbsp;
                                    </label>
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2">&nbsp;</label>
                                <button class="btn btn-primary" type="submit">保存</button>
                            </div>
                        </form>
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
        // $(function() {
        //     var upUrl = "<?php echo U('Admin/UploadFile/upimage');?>";
        //     var delUrl = "<?php echo U('Admin/UploadFile/delete');?>";
        //     //封面图片
        //     var img = "<?php echo ($info['img']); ?>";
        //     upimage("img", "img", upUrl, delUrl, img);
        // });
        $("#type").change(function () {
            var val = $("#type").val();
            if( val == 2 ){
                $('#goldDiv').show();
            }else{
                $('#goldDiv').hide();
            }
        });
        $("#cate").change(function () {
            var val = $("#cate").val();
            if( val == 2 ){
                $('#typeDiv').hide();
                $('#goldDiv').hide();
            }else{
                $('#typeDiv').show();
            }
        });
    </script>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>