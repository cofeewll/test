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
<style type="text/css">
	.line{display: inline-block;width: 20%;}
	.h34{line-height: 34px;}
	.w13{width: 13%;}
	.w15{width: 15%;}
	.block{display: block;clear: both;}
</style>

</head>

    <body class="gray-bg">
    <div class="wrapper wrapper-content  animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form role="form" action="<?php echo U('Wheel/config');?>" id="form" class="form-horizontal">
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >抽奖消耗积分</label>
                                <div class="col-sm-10 shot">
                                    <input type="number" min="0" readonly class="form-control" value="10" name="score" style="width:60%;" autocomplete="off">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                             <div class="form-group">
                                <label class="col-sm-2 control-label" >配置奖品信息<br/><span style="color: red;font-size: 12px;">(所有奖品中奖比率之和应等于100)</span></label>
                                <div class="col-sm-10 shot">
                                    <div id="prizeDiv">
				                        <?php if(empty($aprize)): ?><div class="block">
				                                <select name="prize[]" class="form-control pull-left line">
				                                    <option value="">选择奖品</option>
				                                    <?php if(is_array($award)): $i = 0; $__LIST__ = $award;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				                                </select>
				                                <label class="pull-left h34">&nbsp;&nbsp;奖品等级：</label>
				                                <input name="level[]" type="number" readonly="true" value="1" min="1" max="99" placeholder="1即一等奖" class="form-control pull-left w13">
				                                <label class="pull-left h34">&nbsp;&nbsp;中奖比率：</label>
				                                <input name="chance[]" type="number" min="1" max="100" placeholder="1-100之间" class="form-control pull-left w15">
				                            </div>
				                        <?php else: ?>
				                            <?php if(is_array($aprize)): $k = 0; $__LIST__ = $aprize;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vol): $mod = ($k % 2 );++$k;?><div class="block">
				                                    <select name="prize[]" class="form-control pull-left line">
				                                        <option value="">选择奖品</option>
				                                        <?php if(is_array($award)): $i = 0; $__LIST__ = $award;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if($vol["pid"] == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				                                    </select>
				                                    <label class="pull-left h34">&nbsp;&nbsp;奖品等级：</label>
				                                    <input name="level[]" type="number" min="1" readonly="true" value="<?php echo ($vol["level"]); ?>" max="99" placeholder="1即一等奖" class="form-control pull-left w13">
				                                    <label class="pull-left h34">&nbsp;&nbsp;中奖比率：</label>
				                                    <input name="chance[]" type="number" min="1" max="100" value="<?php echo ($vol["chance"]); ?>" placeholder="1-100之间" class="form-control pull-left w15">
				                                    <?php if(($k) > "1"): ?><a href="javascript:void;" onclick="delLine(this)">&nbsp;&nbsp;删除</a><?php endif; ?>
				                                </div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
				                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                	<label class="col-sm-2" >&nbsp;</label>
                                	<div class="col-sm-10 shot">
                                		<a href="javascript:addPrize();">增加奖项</a>
                                	</div>
			                    </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >抽奖规则</label>
                                <div class="col-sm-10 shot">
                                    <textarea id="editor" name="rule" style="width:60%;height: 300px;">
                                    	<?php if(!empty($info)): echo ($info['rule']); endif; ?>
                                    </textarea>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            
                           
                            <div class="form-group">
                                <label class="col-sm-2 control-label">活动状态</label>
                                <div class="col-sm-10 shot">
                                    <label class="i-checks">
                                        <input type="radio" value="1" name="status" <?php if(!empty($info)): if($info['status'] == 1): ?>checked<?php endif; else: ?>checked<?php endif; ?> > <i></i> 正常&nbsp;&nbsp;&nbsp;&nbsp;
                                    </label>
                                    <label class="i-checks">
                                        <input type="radio" value="0" name="status" <?php if(!empty($info)): if($info['status'] == 0): ?>checked<?php endif; endif; ?> > <i></i> 暂停&nbsp;&nbsp;&nbsp;&nbsp;
                                    </label>
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2">&nbsp;</label>
                                <button class="btn btn-primary" type="submit">保存</button>
                            </div>
                        </form>
                        <div id="temp" style="display: none;">
                        <div class="block">
                            <select name="prize[]" class="form-control pull-left line">
                                <option value="">选择奖品</option>
                                <?php if(is_array($award)): $i = 0; $__LIST__ = $award;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                            <label class="pull-left h34">&nbsp;&nbsp;奖品等级：</label>
                            <input name="level[]" type="number" min="1" readonly="true" max="99" placeholder="1即一等奖" class="form-control pull-left w13">
                            <label class="pull-left h34">&nbsp;&nbsp;中奖比率：</label>
                            <input name="chance[]" type="number" min="1" max="100" placeholder="1-100之间" class="form-control pull-left w15">
                            <a href="javascript:void;" class="h34" onclick="delLine(this)">&nbsp;&nbsp;删除</a>
                        </div>
                    </div>
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

        $("#form").submit(function() {
            var tempArr = new Array();
            var flag = true;
            $("#prizeDiv select[name='prize[]'] option:selected").each(function(){
                var val = $.trim($(this).val());
                if(val == ''){
                    layer.msg('奖品信息填写不完整', {
                        icon: 0,
                        offset: 0,
                        shift: 6,
                        time: 1500
                    });
                    flag = false;
                }else{
                    if( tempArr && $.inArray(val,tempArr)!=-1 ){
                        layer.msg('选择的奖品重复', {
                            icon: 0,
                            offset: 0,
                            shift: 6,
                            time: 1500
                        });
                        flag = false;
                    }else{
                        tempArr.push(val);
                    }
                }
            });
            var totalChance = 0;
            $("#prizeDiv input[name='chance[]']").each(function(){
                if($.trim($(this).val()) == ''){
                    layer.msg('奖品信息填写不完整', {
                        icon: 0,
                        offset: 0,
                        shift: 6,
                        time: 1500
                    });
                    flag = false;
                }
                totalChance += parseFloat($(this).val());
            });
            if(!flag){
                return false;
            }
            if(100 != totalChance){
                layer.msg('所有奖品比率之和须等于100', {
                        icon: 0,
                        offset: 0,
                        shift: 6,
                        time: 2000
                    });
                return false;
            }
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

        /**
         *[addPrize 增加奖项按钮]
         */
        function addPrize(){
            var tempStr = $("#temp").html();
            var flag = true;
            $("#prizeDiv select[name='prize[]'] option:selected").each(function(){
                if($.trim($(this).val()) == ''){
                    layer.msg('已有信息填写不完整', {
                        icon: 0,
                        offset: 0,
                        shift: 6,
                        time: 1500
                    });
                    flag = false;
                }
            });
            $("#prizeDiv input[name='chance[]']").each(function(){
                if($.trim($(this).val()) == ''){
                    layer.msg('已有信息填写不完整', {
                        icon: 0,
                        offset: 0,
                        shift: 6,
                        time: 1500
                    });
                    flag = false;
                }
            });
            var length = $("#prizeDiv input[name='chance[]']").length;
            if(flag){
                $("#prizeDiv").append(tempStr);
                $("#prizeDiv input[name='level[]']").each(function(){
                    if($.trim($(this).val()) == ''){
                        $(this).val(length+1);
                    }
                });
            }
        }
        
        function delLine(obj){
            $(obj).parent().remove();
        }
    </script>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>