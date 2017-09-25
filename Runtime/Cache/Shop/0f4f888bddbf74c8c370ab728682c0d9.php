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
                <div class="ibox ">
                    <div class="ibox-content">
                        <form class="form-inline" id="form-search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="name" id="name" style="width:150px;" placeholder="商品名称">
                            </div>
                            <div class="input-group">
                                <select class="form-control" name="cid">
                                    <option value="">商品分类</option>
                                    <?php if(is_array($cates)): $i = 0; $__LIST__ = $cates;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                            <div class="input-group">
                                <select class="form-control" name="status">
                                    <option value="">状态</option>
                                    <option value="1">上架</option>
                                    <option value="0">下架</option>
                                    <option value="-1">待审核</option>
                                    <option value="-2">拒绝通过</option>
                                    <option value="-3">强制下架</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn-sm btn-primary" type="button" id ="search-btn" ><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                            <div class="input-group">
                                <button class="btn-sm btn-primary " type="button" id="search-clear"><i class="fa fa-undo"></i></button>
                                
                            </div>
                        </form>
                    </div>
                    <div class="ibox-content">
                        <div class="jqGrid_wrapper">
                            <table id="table" style="border-collapse: collapse"></table>
                            <div id="pager"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
<script>
var dataUrl = "<?php echo ($dataUrl); ?>";
var editUrl = "<?php echo ($editUrl); ?>";
var addUrl = "<?php echo ($addUrl); ?>";
var showUrl = "<?php echo ($showUrl); ?>";
var delUrl = "<?php echo ($delUrl); ?>";
$(function(){
    $(document).ready(function() {
        $(window).bind("resize",function(){
            var width=$(".jqGrid_wrapper").width();
            $("#table").setGridWidth(width);
        })
    });
    $.jgrid.defaults.styleUI="Bootstrap";
    var lastsel;
        $("#table").jqGrid({
            url:dataUrl,
            datatype: "json",
            colNames:['ID','商品名称','货号','商品分类','价格','店铺首页','上/下架','库存','排序','操作'],
            colModel:[
                {name:'id',index:'id', width:55,align:'center'},
                {name:'name',index:'name',editable: true,sortable: false,align:'center',width:400},
                {name:'goodsSn',index:'goodsSn',editable: false,sortable: false,align:'center',width:100},
                {name:'cid',index:'cid',editable: false,sortable: false,align:'center',width:100},
                {name:'price',index:'price',editable: true,sortable: false,align:'center',width:100},
                {name:'isIndex',index:'isIndex',editable: false,sortable: false,align:'center',width:80,formatter:function (a,b,c){
                    if(a == 0){
                        val = '<i class="fa fa-close" style="color:red"></i>';
                    } 
                    else{
                        val = '<i class="fa fa-check" style="color:#00a157"></i>';
                    } 
                    return '<a href="javascript:void(0);" onclick="changeShow('+c.id+','+a+')">'+val +'</a>';
                }},
                {name:'status',index:'status',editable: false,sortable: false,align:'center',width:100,formatter:function (a,b,c){
                    if(a == -1){
                        return '<font style="color: #ff0aff">待审核</font>';
                    }else if(a == -2){
                        return '<font style="color: red;">拒绝通过</font>';
                    }else if(a == -3){
                        return '<font style="color: red;">强制下架</font>';
                    }else{
                        if(a == 0){
                            val = '<i class="fa fa-close" style="color:red"></i>';
                        } 
                        else{
                            val = '<i class="fa fa-check" style="color:#00a157"></i>';
                        } 
                        return '<a href="javascript:void(0);" onclick="changeStatus('+c.id+','+a+')">'+val +'</a>';
                    }
                    
                }},
                {name:'stock',index:'stock',editable: false,sortable: true,align:'center',width:100},
                {name:'sorts',index:'sorts',editable: true,sortable: true,align:'center',width:100},
                {width:100,sortable: false,align:'center',formatter: function (a, b, c) {
                    var btn = '';
                        btn += '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="addFun(' + c.id + ')" ><i title="编辑" class="fa fa-edit"></i></a>';
                        btn += '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteFun(' + c.id + ')"><i title="删除" class="fa fa-trash"></i></a>';
                        
                        return btn;  
                    }
                },
            ],
            rowNum:10,
            rowList:[10,20,30],
            pager: '#pager',
            sortname: 'id',
            viewrecords: true,
            autowidth:true,
            height: 'auto',
            emptyrecords: "暂无数据",
            sortorder: "desc",
            caption:"<a href='javascript:void(0)' onclick='addFun()'><i class='fa fa-plus'></i>添加</a>",
            onselectrow:false,
            onSelectRow:function(){
                $('#table').jqGrid('restoreRow',lastsel);
            },
            ondblClickRow: function(id){
                $('#table').jqGrid('editRow',id);
                lastsel = id;
            },
        });
   $('#table').delegate('input.editable','keypress', function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) { //Enter keycode
            var rowData = $('#table').getRowData(lastsel);
            var name = $.trim($("#"+lastsel+"_name").val());
            var price = parseFloat($("#"+lastsel+"_price").val());
            if( name == ''){
                layer.msg("请输入商品名称");
                return false;
            }
            if(price == NaN || price<0){
                layer.msg("商品价格格式不正确");
                return false;
            }
            $('#table').jqGrid('saveRow',lastsel,{
                url: editUrl,
                mtype : "POST",
                extraparam : {id:rowData.id},
                restoreAfterError: true,
                successfunc: function(response){
                    layer.msg(response.responseJSON.info);
                    $("#table").trigger("reloadGrid");
                    if( response.responseJSON.status ){
                        return true;
                    } else {
                        return false;
                    }
                },
                errorfunc: function(rowid, res){
                    var data = JSON.parse(res.responseText);
                    layer.msg(data.info);
                }
            });
        }
    });
})

        
    </script>
    <script type="text/javascript">
        
        function changeShow(id,value){
            var newUrl = "<?php echo ($newUrl); ?>";
            $.ajax({
            url: newUrl,
            data: {"id": id,"value":value},
            type: "post",
            success: function (data) {
                if (data.status) {
                    $("#table").trigger("reloadGrid");
                } else {
                    layer.msg(data.info, {
                        icon:0,
                        offset: 0,
                        shift: 6,
                        time:1500
                    });
                }
            }, 
            error: function (error) {
                alert(data.info);
            }
        });
        }
    </script>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>