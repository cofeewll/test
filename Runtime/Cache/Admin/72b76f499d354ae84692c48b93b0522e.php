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
                                <input type="text" class="form-control" name="name" id="name" style="width:150px;" placeholder="奖品名称">
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control" name="nickname" id="nickname" style="width:150px;" placeholder="用户名称">
                            </div>
                            <div class="input-group">
                                <select class="form-control" name="type">
                                    <option value="">奖品类型</option>
                                    <option value="1">特殊奖</option>
                                    <option value="0">金币</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <select class="form-control" name="isDeal">
                                    <option value="">状态</option>
                                    <option value="1">已派奖</option>
                                    <option value="0">待派奖</option>
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
var delUrl = "<?php echo ($delUrl); ?>";
var dealUrl = "<?php echo ($dealUrl); ?>";
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
            colNames:['ID','中奖用户','奖品名称','奖品类型','派奖状态','中奖时间','操作'],
            colModel:[
                {name:'id',index:'id', width:55,align:'center'},
                {name:'nickname',index:'nickname',editable: false,sortable: false,align:'center'},
                {name:'name',index:'name',editable: false,sortable: false,align:'center'},
                {name:'type',index:'type',editable: false,sortable: false,align:'center'}, 
                {name:'isDeal',index:'isDeal',editable: false,sortable: false,align:'center',formatter:function (a,b,c){
                    if(a == 0){
                        val = '<i class="fa fa-close" style="color:red"></i>';
                    } 
                    else{
                        val = '<i class="fa fa-check" style="color:#00a157"></i>';
                    } 
                    return '<a href="javascript:void(0);">'+val +'</a>';
                }},
                {name:'createTime',index:'createTime',editable: false,sortable: true,align:'center'},  
                {width:100,sortable: false,align:'center',formatter: function (a, b, c) {
                    var btn ='';
                        if(c.type == '特殊奖'){
                            btn += '<a href="javascript:void(0);" onclick="dealFun(' + c.id + ')"><i title="派奖信息" class="fa fa-gift"></i></a>';
                        }
                        
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
            caption:"中奖记录",
            onselectrow:false,
        });
})

        
    </script>
    <script type="text/javascript">
        function dealFun(id){
            url = dealUrl+'/id/'+id;
            layer.open({
                type: 2,
                title: '编辑派奖信息',
                shadeClose: true,
                shade: 0.3,
                area: ['80%', '95%'],
                content: url, 
                end: function () {
                    $("#table").trigger("reloadGrid");
                }
            });
        }
    </script>



<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>