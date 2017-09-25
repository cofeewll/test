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
                        <h5>店铺信息</h5>
                    </div>
                    <div class="ibox-content">
                        <form id="form_shop">
                        <table class="table table-bordered">
                            <tbody>
                            <tr><td style="width:200px;">店铺名称</td><td><input type="text" name="title" value="<?php echo ($data["title"]); ?>" class="form-control"></td></tr>
                            <tr>
                                <td style="width:200px;">店铺logo<br/>(尺寸：宽160*高90px；大小：限5M内)</td>
                                <td>
                                    <div id="img"></div>
                                </td>
                            </tr>
                            <tr><td style="width:200px;">商家信息</td><td><textarea class="form-control" name="description"><?php echo ($data["description"]); ?></textarea></td></tr>
                            <tr><td style="width:200px;">商家地址</td><td><input class="form-control" name="address" value="<?php echo ($data["address"]); ?>"></td></tr>
                            <tr><td style="width:200px;">商家电话</td><td><input class="form-control" name="tel" value="<?php echo ($data["tel"]); ?>"></td></tr>
                            <tr><td style="width:200px;">客服QQ</td><td><input class="form-control" name="qq" value="<?php echo ($data["qq"]); ?>"></td></tr>
                            <tr>
                                <td style="width:200px;">满</td>
                                <td>
                                    <input class="form-control" name="fullMoney" value="<?php echo ($data["fullMoney"]); ?>" style="width:10%;display:inline;">
                                    &nbsp;元包邮</td>
                            </tr>
                            <tr>
                                <td style="width:200px;">设置不包邮省份</td>
                                <td>
                                    <table class="table table-bordered">
                                        <tr>
                                            <textarea class="form-control"><?php echo ($data["provinces"]); ?></textarea>
                                        </tr>
                                        <tr>
                                            <br>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <select data-placeholder="选择省份" class="chosen-select" multiple style="width:1000px;display:inline;" tabindex="4">
                                                        <option value="">请选择省份</option>
                                                        <option value="110000" hassubinfo="true">北京</option>
                                                        <option value="120000" hassubinfo="true">天津</option>
                                                        <option value="130000" hassubinfo="true">河北省</option>
                                                        <option value="140000" hassubinfo="true">山西省</option>
                                                        <option value="150000" hassubinfo="true">内蒙古自治区</option>
                                                        <option value="210000" hassubinfo="true">辽宁省</option>
                                                        <option value="220000" hassubinfo="true">吉林省</option>
                                                        <option value="230000" hassubinfo="true">黑龙江省</option>
                                                        <option value="310000" hassubinfo="true">上海</option>
                                                        <option value="320000" hassubinfo="true">江苏省</option>
                                                        <option value="330000" hassubinfo="true">浙江省</option>
                                                        <option value="340000" hassubinfo="true">安徽省</option>
                                                        <option value="350000" hassubinfo="true">福建省</option>
                                                        <option value="360000" hassubinfo="true">江西省</option>
                                                        <option value="370000" hassubinfo="true">山东省</option>
                                                        <option value="410000" hassubinfo="true">河南省</option>
                                                        <option value="420000" hassubinfo="true">湖北省</option>
                                                        <option value="430000" hassubinfo="true">湖南省</option>
                                                        <option value="440000" hassubinfo="true">广东省</option>
                                                        <option value="450000" hassubinfo="true">广西壮族自治区</option>
                                                        <option value="460000" hassubinfo="true">海南省</option>
                                                        <option value="500000" hassubinfo="true">重庆</option>
                                                        <option value="510000" hassubinfo="true">四川省</option>
                                                        <option value="520000" hassubinfo="true">贵州省</option>
                                                        <option value="530000" hassubinfo="true">云南省</option>
                                                        <option value="540000" hassubinfo="true">西藏自治区</option>
                                                        <option value="610000" hassubinfo="true">陕西省</option>
                                                        <option value="620000" hassubinfo="true">甘肃省</option>
                                                        <option value="630000" hassubinfo="true">青海省</option>
                                                        <option value="640000" hassubinfo="true">宁夏回族自治区</option>
                                                        <option value="650000" hassubinfo="true">新疆维吾尔自治区</option>
                                                        <option value="710000" hassubinfo="true">台湾省</option>
                                                        <option value="810000" hassubinfo="true">香港特别行政区</option>
                                                        <option value="820000" hassubinfo="true">澳门特别行政区</option>
                                                        <option value="990000" hassubinfo="true">海外</option>
                                                    </select>&nbsp;&nbsp;<button type="button" class="btn-xs btn-info" onclick="set_provinces()">重新设置</button>
                                                </div>

                                            </div>
                                        </tr>
                                    </table>

                                </td>
                            </tr>
                            <tr><td style="width:200px;">营业执照</td>
                                <td><div id="license"></div></td>
                            </tr>
                            <tr>
                                <td style="width:200px;">商家相册 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="btn-xs btn-primary" data-toggle="modal" href="#modal-form"><i class="fa fa-plus"></i>添加</a>
                                <br/>(尺寸：宽320*高230px；大小：限5M内)
                                </td>
                                <td>
                                    <table class="table table-bordered" style="width:30%;">
                                        <?php if(is_array($picture)): $i = 0; $__LIST__ = $picture;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                                                <td><img src="<?php echo ($v["path"]); ?>" style="wdith:100px;height:80px;"></td>
                                                <td>
                                                    <a class="btn-xs btn-info" data-toggle="modal" href="#modal-form1" style="display:none;"></a>
                                                    <button class="btn-xs btn-info" onclick="edit(this,<?php echo ($v["id"]); ?>)" type="button"><i class="fa fa-edit"></i>编辑</button>
                                                    <button class="btn-xs btn-danger" onclick="del(<?php echo ($v["id"]); ?>)" type="button"><i class="fa fa-trash"></i>删除</button>
                                                </td>
                                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px;">店铺轮播图 &nbsp;&nbsp;<a class="btn-xs btn-primary" data-toggle="modal" href="#modal-form2"><i class="fa fa-plus"></i>添加</a><br/>(尺寸：宽750*高260px；大小：限5M内)</td>
                                <td>
                                    <table class="table table-bordered" style="width:30%;">
                                        <?php if(is_array($banner)): $i = 0; $__LIST__ = $banner;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                                                <td><img src="<?php echo ($v["path"]); ?>" style="wdith:100px;height:80px;"></td>
                                                <td>
                                                    <a class="btn-xs btn-info" data-toggle="modal" href="#modal-form1" style="display:none;"></a>
                                                    <button class="btn-xs btn-info" onclick="edit(this,<?php echo ($v["id"]); ?>)" type="button"><i class="fa fa-edit"></i>编辑</button>
                                                    <button class="btn-xs btn-danger" onclick="del(<?php echo ($v["id"]); ?>)" type="button"><i class="fa fa-trash"></i>删除</button>
                                                </td>
                                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px;">操作</td>
                                <td><button class="btn-sm btn-primary">保存</button></td>
                            </tr>
                            </tbody>
                        </table>
                        </form>
                    </div>
                </div>
            </div>



        </div>
    </div>
    <div id="modal-form" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="m-t-none m-b">添加商家相册</h3>
                            <form role="form">
                                <div class="form-group">
                                    <label>相册图片（建议尺寸200*200）：</label>
                                    <div id="picture0"></div>
                                </div>
                                <div>
                                    <span class="btn  btn-primary  m-t-n-xs" type="submit" id="submit"><strong>保存</strong>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-form2" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="m-t-none m-b">添加店铺轮播图</h3>
                            <form role="form">
                                <div class="form-group">
                                    <label>图片（建议尺寸200*200）：</label>
                                    <div id="banner0"></div>
                                </div>
                                <div>
                                    <span class="btn  btn-primary  m-t-n-xs" type="submit" id="submit2"><strong>保存</strong>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-form1" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="m-t-none m-b">编辑</h3>
                            <form role="form">
                                <div class="form-group">
                                    <label>上传图片（建议尺寸200*200）：</label>
                                    <div id="picture1"></div>
                                </div>
                                <div>
                                    <input name="id" type="hidden">
                                    <span class="btn  btn-primary  m-t-n-xs" type="submit" id="submit1"><strong>保存</strong>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>


    <link href="/Public/Admin/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/Public/Admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/Public/Admin/css/plugins/chosen/chosen.css" rel="stylesheet">
    <link href="/Public/Admin/css/plugins/colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link href="/Public/Admin/css/plugins/cropper/cropper.min.css" rel="stylesheet">
    <link href="/Public/Admin/css/plugins/switchery/switchery.css" rel="stylesheet">
    <link href="/Public/Admin/css/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">
    <!--<link href="/Public/Admin/css/plugins/nouslider/jquery.nouislider.css" rel="stylesheet">-->
    <link href="/Public/Admin/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
    <link href="/Public/Admin/css/plugins/ionRangeSlider/ion.rangeSlider.css" rel="stylesheet">
    <link href="/Public/Admin/css/plugins/ionRangeSlider/ion.rangeSlider.skinFlat.css" rel="stylesheet">
    <link href="/Public/Admin/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
    <link href="/Public/Admin/css/plugins/clockpicker/clockpicker.css" rel="stylesheet">
    <link href="/Public/Admin/css/animate.min.css" rel="stylesheet">
    <link href="/Public/Admin/css/style.min862f.css?v=4.1.0" rel="stylesheet">

    <script>
        $(function() {
            var upUrl = "/index.php/Admin/UploadFile/upimage";
            var delUrl = "/index.php/Admin/UploadFile/delete";
            //封面图片
            var bImg = "<?php echo ($data['img']); ?>";
            upimage("img", "img", upUrl, delUrl, bImg);
            upimage("license", "license", upUrl, delUrl, "<?php echo ($data["license"]); ?>");
            for(var i=0;i<10;i++){
                upimage("picture"+i, "picture", upUrl, delUrl, '');
                upimage("banner"+i, "banner", upUrl, delUrl, '');
            }
            $(".uploadify-queue-item").css({"width":"100px"})
            $(".showthumb").css({"minHeight":"100px"});
            $("span.delbtn").css({"display":"none"})
        });
        //添加相册
        $("#submit").click(function(){
            if($("input[name=picture]").val()){

            }else{
                layer.msg("请上传图片");return ;
            }
            $(this).attr("id","");
            $.post("<?php echo U('picture');?>",{type:'add',picture:$("input[name=picture]").val()},function(ret){
                layer.msg(ret.msg);
                if(ret.status==1){
                    $(this).attr("id","submit");
                    location.reload();
                }

            })
        })
        //添加相册
        $("#submit2").click(function(){
            if($("input[name=banner]").val()){

            }else{
                layer.msg("请上传图片");return ;
            }
            $(this).attr("id","");
            $.post("<?php echo U('banner');?>",{type:'add',picture:$("input[name=banner]").val()},function(ret){
                layer.msg(ret.msg);
                if(ret.status==1){
                    $(this).attr("id","submit2");
                    location.reload();
                }

            })
        })
        function edit(v,id){
            $(v).parent().find("a").click();
            $("#modal-form1").find("input[name=id]").val(id);
        }
        function del(id){
            $.post("<?php echo U('picture');?>",{type:'del',id:id},function(ret){
                layer.msg(ret.msg);
                if(ret.status==1){
                    location.reload();
                }

            })
            return ;
        }
        //编辑相册
        $("#submit1").click(function(){
            if($("input[name=picture]").val()){

            }else{
                layer.msg("请上传图片");return ;
            }
            var id=$("input[name=id]").val();
            if(id==''){
                layer.msg("系统错误");return ;
            }
            $(this).attr("id","");
            $.post("<?php echo U('picture');?>",{type:'edit',picture:$("input[name=picture]").val(),id:id},function(ret){
                layer.msg(ret.msg);
                if(ret.status==1){
                    $(this).attr("id","submit1");
                    location.reload();
                }

            })
        })
        $("#form_shop").submit(function(){
            $.post("<?php echo U('editShop');?>",$(this).serialize(),function(ret){
                layer.msg(ret.msg);
            })
            return false;
        })
        function set_provinces(){
            var obj=$("li.search-choice");
            var str="";
            for(var i=0;i<obj.length;i++){
                str+=obj.eq(i).find("span").text()+"|";
            }
            $.post("<?php echo U('setProvinces');?>",{str:str},function(ret){
                layer.msg(ret.msg);location.reload();
            })
        }
    </script>
    <script src="/Public/Admin/js/plugins/chosen/chosen.jquery.js"></script>
    <script src="/Public/Admin/js/plugins/jsKnob/jquery.knob.js"></script>
    <script src="/Public/Admin/js/plugins/jasny/jasny-bootstrap.min.js"></script>
    <script src="/Public/Admin/js/plugins/datapicker/bootstrap-datepicker.js"></script>
    <script src="/Public/Admin/js/plugins/prettyfile/bootstrap-prettyfile.js"></script>

    <script src="/Public/Admin/js/demo/form-advanced-demo.min.js"></script>
    <link rel="stylesheet" href="/Public/Static/Huploadify/Huploadify.css">
    <script charset="utf-8" src="/Public/Static/Huploadify/jquery.Huploadify.js"></script>
    <script charset="utf-8" src="/Public/Static/Huploadify/Huploadify.js"></script>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>