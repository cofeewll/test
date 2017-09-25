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
					<form role="form" action="<?php echo U('Manager/rolesAddSave');?>" id="form-add" class="form-horizontal">
						<input type="hidden" name="id" <?php if(!empty($info)): ?>value="<?php echo ($info["id"]); ?>"<?php endif; ?> />
						 
						<div class="form-group">
							<label class="col-sm-2 control-label">角色名称</label>
							<div class="col-sm-10 shot">
								<input type="text" class="form-control" style="width:60%;display:inline-block;" <?php if(!empty($info)): ?>value="<?php echo ($info['title']); ?>"<?php endif; ?> id="title" name="title" autocomplete="off" placeholder="名称">
						<span style="color: red;font-size:20px;" >&nbsp;*</span>
							</div>
						
						</div>
						<div class="hr-line-dashed"></div>  
						<div class="form-group">
							<label class="col-sm-2 control-label">角色状态</label>
							<div class="col-sm-10 shot">
								<label class="i-checks">
		                            <input type="radio" value="1" name="status" <?php if(!empty($info)): if($info['status'] == 1): ?>checked<?php endif; else: ?>checked<?php endif; ?> > <i></i> 正常&nbsp;&nbsp;&nbsp;&nbsp;
		                        </label>
		                        <label class="i-checks">
		                            <input type="radio" value="0" name="status" <?php if(!empty($info)): if($info['status'] == 0): ?>checked<?php endif; endif; ?> > <i></i> 禁用
		                        </label>
	                        </div>
						</div>
						<div class="hr-line-dashed"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label">角色权限</label>
							<div class="col-sm-10 shot">
							<table class="table table-bordered">
								<?php if(is_array($rules)): foreach($rules as $key=>$v): if($v['id'] == 1): ?><input type="checkbox" style="display:none;" checked name="rules[]" value="<?php echo ($v['id']); ?>">
								<?php else: ?>
									<tr>
										<td style="width:20%;">
											<label>
												<input type="checkbox" class="rules0" name="rules[]" value="<?php echo ($v['id']); ?>"  <?php if(!empty($info)): if(in_array($v['id'],$info['rules'])){echo 'checked';} endif; ?>> <?php echo ($v['title']); ?>  
											</label>
										</td>
										<td style="width:80%;">
											<?php if(is_array($v['child'])): foreach($v['child'] as $key=>$vo): ?><label>
												<input type="checkbox" class="rules1" name="rules[]" value="<?php echo ($vo['id']); ?>"  <?php if(!empty($info)): if( in_array($vo['id'],$info['rules'])){echo 'checked';} endif; ?>> <?php echo ($vo['title']); ?> &nbsp; &nbsp;
											</label><?php endforeach; endif; ?>
										</td>
									</tr><?php endif; endforeach; endif; ?>
							</table>
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
<script>

$(function () {
		
	$('input[type="checkbox"].rules0').on('click',function(){
		var value = $(this).prop('checked'); 
		var input = $(this).closest('td').next('td').find('.rules1');
		if(value == true){
				$.each( input, function(i, n){
				$(this).prop('checked',true);
			});
		}else{
			$.each( input, function(i, n){
				$(this).prop('checked',false);
			}); 
		}

	});
	
		$('input[type="checkbox"].rules1').on('click',function(){
		var value = $(this).prop('checked'); 
		var input = $(this).closest('td').find('.rules1'); 
		var oneinput = $(this).closest('td').prev('td').find('.rules0');
		if(value == true){
			oneinput.prop('checked',true);
		}else{
			var all = true;
			$.each( input, function(i, n){
				if($(this).prop('checked') == true){
					all = false;
				}
			});
			if(all == true){
				oneinput.prop('checked',false);
			}
				
		}
	});
});
</script>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>