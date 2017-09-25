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
                <div class="ibox ">
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
var delUrl = "<?php echo ($delUrl); ?>";
var showUrl = "<?php echo ($showUrl); ?>";
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
            colNames:['ID','角色名称', '角色状态','操作'],
            colModel:[
                {name:'id',index:'id', width:55, align: 'center'},
                {name:'title',index:'title',editable: true,sortable: false, align: 'center'},
                {name:'status',index:'status', align: 'center',formatter:function (a,b,c){
                    if(a == 0){
                        val = '<i class="fa fa-close" style="color:red"></i>';
                    } 
                    else{
                        val = '<i class="fa fa-check" style="color: #00a157"></i>';
                    } 
                    return '<a href="javascript:void(0);" onclick="changeStatus('+c.id+','+a+')">'+val +'</a>';
                }},
                {width:100,sortable: false, align: 'center',formatter: function (a, b, c) {
                    var btn = '<a href="javascript:void(0);" onclick="addFun(' + c.id + ')"><i class="fa fa-edit"></i></a>';
                        if( c.id != 1 ) {
                                btn += '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteFun(' + c.id + ')"><i class="fa fa-trash"></i></a>';
                        }
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
            sortorder: "asc",
            caption:"<a href='javascript:void(0)' onclick='addFun()'><i class='fa fa-plus'></i>添加</a>",
            onselectrow:true,
            onSelectRow:function(){
                //$('#table').jqGrid('restoreRow',lastsel);
            },
            ondblClickRow: function(id){
                //$('#table').jqGrid('editRow',id);
                //lastsel = id;
            }, 
        });

       $('#table').delegate('input.editable','keypress', function(e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (code == 13) { //Enter keycode
                var rowData = $('#table').getRowData(lastsel);

                var bSortVal = $("#"+lastsel+"_bSort").val();
                if(!isNumber(bSortVal)){
                    layer.msg("排序格式错误");
                    return false;
                }
                
                $('#table').jqGrid('saveRow',lastsel,{
                    url: "<?php echo U('Banner/editRow');?>",
                    mtype : "POST",
                    extraparam : {bId:rowData.bId},
                    restoreAfterError: true,
                    successfunc: function(response){
                        layer.msg(response.responseJSON.info);
                        if( response.responseJSON.status ){
                            return true;
                        } else {
                            return false;
                        }
                    },
                    errorfunc: function(rowid, res){
                        layer.msg('数据已存在');
                    }
                });
            }
        }); 
    
})

        
    </script>



<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>