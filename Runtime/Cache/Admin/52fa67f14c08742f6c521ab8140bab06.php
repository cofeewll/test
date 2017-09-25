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
                <div class="ibox ">
                    <div class="ibox-content">
                        <form class="form-inline" id="form-search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="title" id="title" style="width:150px;" placeholder="商家名称">
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control" name="username" id="username" style="width:150px;" placeholder="商家账号">
                            </div>

                            <div class="input-group">
                                <input type="text" class="form-control" name="phone" id="phone" style="width:150px;" placeholder="手机号码">
                            </div>
                            <div class="input-group">
                                <select class="form-control" name="status">
                                    <option value="">商家状态</option>
                                    <option value="1">正常</option>
                                    <option value="0">禁用</option>
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
            colNames:['商家账号','商家名称','手机号','手续费比例','商家类别','状态','入驻时间','操作','ID'],
            colModel:[
                {name:'username',index:'username',editable: false,sortable: false,align:'center',width:100},
                {name:'title',index:'title',editable: false,sortable: false,align:'center',width:200,formatter:function (a,b,c){

                    return '<a href="javascript:void(0);" onclick="addFun('+c.id+')">'+a +'</a>';
                }},
                {name:'phone',index:'phone',editable: false,sortable: false,align:'center',width:100}, 
                {name:'shopFee',index:'shopFee',editable: false,sortable: false,align:'center',width:100}, 
                {name:'cate',index:'cate',editable: false,sortable: false,align:'center',width:100},   		
                {name:'status',index:'status',editable: false,sortable: false,align:'center',width:100,formatter:function (a,b,c){
                    if(a == 0){
                        val = '<i class="fa fa-close" style="color:red"></i>';
                    } 
                    else{
                        val = '<i class="fa fa-check" style="color: #00a157"></i>';
                    } 
                    // return val;
                    return '<a href="javascript:void(0);" onclick="changeStatus('+c.id+','+a+')">'+val +'</a>';
                }},
                {name:'regTime',index:'regTime',editable: false,sortable: true,align:'center',width:100},  
                {width:100,sortable: false,align:'center',formatter: function (a, b, c) {
                    var btn = '<a href="javascript:void(0);" onclick="addFun(' + c.id + ')" ><i title="编辑" class="fa fa-edit"></i></a>';
                        
                            btn += '&nbsp;<a href="javascript:void(0);" onclick="deleteFun(' + c.id + ')"><i class="fa fa-trash"></i></a>';
                        
                        return btn;  
                    }
                },
                {name:'id',index:'id', width:55,align:'center',hidden:true},
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
            caption:"商家列表&nbsp;&nbsp;<a href='javascript:void(0)' onclick='addFun()'><i class='fa fa-plus'></i>添加</a>",
            onselectrow:false,
        });

})

        
    </script>



<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>