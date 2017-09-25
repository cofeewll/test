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
 

</head>

    <body class="gray-bg">
    <div class="wrapper wrapper-content  animated fadeInRight">
        <div class="row">
            <div class="col-sm-12 ibox float-e-margins">
                <div class="tabs-container">
                    <div class="ibox-title" style="border-bottom: 0;border-top: 0;">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">基本信息</a>
                            </li>
                            <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">图文信息</a>
                            </li>
                            <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false">商品模型</a>
                            </li>
                            <li class=""><a data-toggle="tab" href="#tab-4" aria-expanded="false">运费配置</a>
                            </li>
                        </ul>
                    </div>
                    <div class="ibox-content" style="border-top: 0;">
                        <form role="form" action="<?php echo U('Goods/addGoods');?>" id="form-add" class="form-horizontal">
                            <input type="hidden" id="goods_id" <?php if(!empty($info)): ?>value="<?php echo ($info['id']); ?>"<?php endif; ?> name="id" />
                            <input type="hidden" name="shopId" value="<?php echo ($info['shopId']); ?>">  
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane active">
                                    
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >商品名称</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['name']); ?>"<?php endif; ?> name="name" style="width:60%;display:inline-block;" autocomplete="off" placeholder="商品名称">
                                            <label style="color: red;font-size:20px;" >&nbsp;*</label>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div> 
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >商品货号</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['goodsSn']); ?>"<?php else: ?>value=""<?php endif; ?> name="goodsSn" style="width:60%;display:inline-block;" autocomplete="off" placeholder="商品货号"><br/>
                                            <label style="color: #9f9f9f;font-size:12px;padding-top: 5px;" >&nbsp;不填写则自动生成</label>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >所属商家</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['sname']); ?>"<?php endif; ?> style="width:60%;display:inline-block;" autocomplete="off" placeholder="所属商家">
                                            <label style="color: red;font-size:20px;" >&nbsp;*</label>
                                        </div>
                                    </div>   
                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >商品分类</label>
                                        <div class="col-sm-10 shot">
                                            <select name="fid" class="form-control" id="fid" style="width:30%;display:inline-block;">
                                            <option value="">请选择</option>
                                                <?php if(is_array($cates)): $i = 0; $__LIST__ = $cates;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(!empty($info)): if(($info["fid"]) == $vo["id"]): ?>selected<?php endif; endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                            </select>
                                            <select name="cid" id="cid" class="form-control" style="width:30%;display:inline-block;">
                                                <option value="">请选择</option>
                                                <?php if(!empty($info["scates"])): if(is_array($info["scates"])): $i = 0; $__LIST__ = $info["scates"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(!empty($info["cid"])): if(($info["cid"]) == $vo["id"]): ?>selected<?php endif; endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                            </select>
                                            <label style="color: red;font-size:20px;" >&nbsp;*</label>
                                        </div>
                                        
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >商品价格</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" name="price" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['price']); ?>"<?php endif; ?> style="width:60%;display:inline-block;">
                                            <label style="color: red;font-size:20px;" >&nbsp;*</label>
                                        </div>
                                        
                                    </div>
                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >商品库存</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" name="stock" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['stock']); ?>"<?php endif; ?> style="width:60%;display:inline-block;">
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >封面图</label>
                                        <div class="col-sm-10 shot">
                                            <div id="img"></div>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>    
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">排序</label>
                                        <div class="col-sm-10 shot">
                                            <input type="number" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['sorts']); ?>"<?php else: ?>value="50"<?php endif; ?> name="sorts"  style="width:60%;"  autocomplete="off" style="width:250px;">
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>
                                    <!-- <div class="form-group">
                                        <label class="col-sm-2">&nbsp;</label>
                                        <button class="btn btn-primary" type="submit">保存</button>
                                    </div> -->
                            
                                </div>
                                <div id="tab-2" class="tab-pane">
                                    
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >商品相册</label>
                                        <div class="col-sm-10 shot">
                                            <div id="images"></div>
                                        </div>
                                        
                                    </div>
                                    <div class="hr-line-dashed"></div> 
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >图文介绍</label>
                                        <div class="col-sm-10 shot">
                                            <textarea class="form-control" name="detail" style="width: 80%;height: 300px;" id="editor"><?php if(!empty($info)): echo ($info['detail']); endif; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>  
                                    <!-- <div class="form-group">
                                        <label class="col-sm-2">&nbsp;</label>
                                        <button class="btn btn-primary" type="submit">保存</button>
                                    </div> -->
                            
                                </div>
                                <div id="tab-3" class="tab-pane">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >商品模型</label>
                                        <div class="col-sm-10 shot">
                                            <select name="typeId" id="spec_type" class="form-control" style="width: 60%;display: inline-block;">
                                                <option value="0">选择商品模型</option>
                                                <?php if(is_array($types)): $i = 0; $__LIST__ = $types;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(!empty($info)): if(($info["typeId"]) == $vo["id"]): ?>selected<?php endif; endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div> 
                                    <div class="form-group">
                                        <!-- ajax 返回规格-->
                                        <label class="col-sm-2 control-label" >&nbsp;</label>
                                        <div id="ajax_spec_data" class="col-sm-8"></div>
                                    </div> 
                                    <div class="hr-line-dashed"></div> 
                                    <!-- <div class="form-group">
                                        <label class="col-sm-2">&nbsp;</label>
                                        <button class="btn btn-primary" type="submit">保存</button>
                                    </div> -->
                                </div>
                                <div id="tab-4" class="tab-pane">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >默认运费</label>
                                        <div class="col-sm-10 shot">
                                            <input type="text" name="shipFee" class="form-control" <?php if(!empty($info)): ?>value="<?php echo ($info['shipFee']); ?>"<?php else: ?>value="0"<?php endif; ?> style="width:60%;display:inline-block;"><br/>
                                            <label style="color: #9f9f9f;font-size:12px;padding-top: 5px;" >&nbsp;0即包邮</label>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" >特殊地区运费配置</label>
                                        <div class="col-sm-10 shot">
                                            <?php if(!empty($provinces)): if(is_array($provinces)): $i = 0; $__LIST__ = $provinces;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div style="padding: 3px 0;">
                                                        <input name="province[]" type="hidden" value="<?php echo ($vo["province"]); ?>">
                                                        <label class="col-sm-2 control-label" ><?php echo ($vo["province"]); ?></label>
                                                        <input type="text" name="fee[]" class="form-control" value="<?php echo ($vo["fee"]); ?>" style="width:25%;display:inline-block;">
                                                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
                                                <label style="color: #9f9f9f;font-size:12px;padding-top: 5px;" >&nbsp;空则与默认运费一致</label>
                                            <?php else: ?>
                                                商家没有配置特殊地区<?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>
                                    <!-- <div class="form-group">
                                        <label class="col-sm-2">&nbsp;</label>
                                        <button class="btn btn-primary" type="submit">保存</button>
                                    </div> -->
                                </div>
                            </div>
                        </form>
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
    </script>
    <link rel="stylesheet" href="/Public/Static/Huploadify/Huploadify.css">
    <script charset="utf-8" src="/Public/Static/Huploadify/jquery.Huploadify.js"></script>
    <script charset="utf-8" src="/Public/Static/Huploadify/Huploadify.js"></script>
    <script>
        $(function() {
            var upUrl = "<?php echo U('Admin/UploadFile/upimage');?>";
            var delUrl = "<?php echo U('Admin/UploadFile/delete');?>";
            //封面图片
            var img = "<?php echo ($info['img']); ?>";
            var images = "<?php echo ($info['images']); ?>";
            upimage("img", "img", upUrl, delUrl, img);
            upimages("images", "images", upUrl, delUrl, images);
        });
    </script>
    <script type="text/javascript">
        /** 以下 商品规格相关 js*/
    $(document).ready(function(){   
    // 商品模型切换时 ajax 调用  返回不同的属性输入框
    $("#spec_type").change(function(){        
        var goods_id = '<?php echo ($info["id"]); ?>';
        var spec_type = $(this).val();
            $.ajax({
                    type:'GET',
                    data:{goods_id:goods_id,spec_type:spec_type}, 
                    url:"<?php echo U('Goods/ajaxGetSpecSelect');?>",
                    success:function(data){                    
                       $("#ajax_spec_data").html('')
                       $("#ajax_spec_data").append(data);
                       ajaxGetSpecInput();  // 触发完  马上触发 规格输入框
                    }
            });           
            //商品类型切换时 ajax 调用  返回不同的属性输入框     
           //  $.ajax({
           //       type:'GET',
           //       data:{goods_id:goods_id,type_id:spec_type}, 
           //       url:"/index.php/admin/Goods/ajaxGetAttrInput",
           //       success:function(data){                            
           //               $("#goods_attr_table tr:gt(0)").remove()
           //               $("#goods_attr_table").append(data);
           //       }        
           // });
    });
    // 触发商品规格
    $("#spec_type").trigger('change'); 
    
    // $("input[name='exchange_integral']").blur(function(){
    //     var shop_price = parseInt($("input[name='shop_price']").val());
    //     var exchange_integral = parseInt($(this).val());
    //     if (shop_price * 100 < exchange_integral) {
            
    //     }
    // });
});
    $("#fid").change(function(){
        var fid = $("#fid").val();
        if(fid){
            $.ajax({
                    type:'GET',
                    data:{fid:fid},
                    dataType:'json' ,
                    url:"<?php echo U('Goods/ajaxGetCates');?>",
                    success:function(data){  
                       $("#cid").html('');
                       console.log(data);
                       var str = '<option value="">请选择</option>';
                       // $.each(data,function(a,b){
                       //      str += '<option value="'+b.id+'">'+b.name+'</option>';
                       // });
                       for(var i=0;i<data.length;i++){
                            str += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                       }
                       $("#cid").append(str);
                    }
            });  
        }
    });

    </script>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>