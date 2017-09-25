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
                        <form role="form" class="form-inline">
                            <div class="form-group">
                                <input class="form-control" name="orderSn" placeholder="请输入订单编号">
                            </div>
                            <div class="form-group">
                                <input class="form-control" name="name" placeholder="请输入商品名称">
                            </div>
                            <div class="form-group">
                                <input class="form-control" name="number" placeholder="请输入用户编号">
                            </div>
                            <div class="form-group">
                                <!--<div class="col-sm-10">-->
                                <input placeholder="开始日期" class="form-control layer-date" id="start" name="start">
                                <input placeholder="结束日期" class="form-control layer-date" id="end" name="end">
                                <!--</div>-->
                            </div>
                            <button class="btn-sm btn-primary " type="button"><i class="fa fa-search" onclick="search_on()"></i></button>
                            <button class="btn-sm btn-info " type="button"><i class="fa fa-undo" onclick="un_do()"></i></button>
                            <!--<button class="btn-sm btn-warning " type="button"><i class="fa fa-download" onclick="excel()"></i></button>-->
                        </form>

                    </div>
                    <div class="ibox-content" id="ibox-content">
                        <div class="jqGrid_wrapper">
                            <table id="table"></table>
                            <div id="pager"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/Public/Admin/js/plugins/layer/laydate/laydate.js"></script>
    <script src="/Public/Admin/js/public.js"></script>
    <script>
        $(document).ready(function() {
            $(window).bind("resize",function(){
                var width=$(".jqGrid_wrapper").width();
                $("#table").setGridWidth(width);
            })
        });
        $.jgrid.defaults.styleUI="Bootstrap";
        $('#table').jqGrid({
            url: "<?php echo U('index');?>",
            datatype: "json",
            colModel: [
                {label: '订单编号', name: 'orderSn', index: 'orderSn',  align: 'center',},
                {label: '商品名称', name: 'name', index: 'name',  align: 'center',width:"200"},
                {label: '商品规格', name: 'spec_key_name', index: 'spec_key_name',  align: 'center',},
                {label: '用户编号', name: 'number', index: 'number',  align: 'center',width:"100"},
                {label: '评价内容',name: 'context', index: 'context',  align: 'center',},
                {label: '显示',  name: 'isShow', index: 'isShow',width:"50",align: 'center', formatter: function (a, b, c) {
                    if (a == 0)
                        return '<a><i class="fa fa-close" style="color:red"></i></a>';
                    if (a == 1)
                        return '<a><i class="fa fa-check" style="color: #00a157"></i></a>';
                } },
                {label: '创建时间', name: 'createTime', index: 'createTime', width: 150, align: 'center', },
                { name: 'id', index: 'id', hidden:true},
            ],
            ondblClickRow: function(id){
                var rowData = $('#table').getRowData(id);
                layer.open({
                    type: 2,
                    title: '查看详细',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['1020px', '90%'],
                    content: "<?php echo U('detail','','');?>/id/"+rowData.id,
                });

            },
            onCellSelect: function (rowid, iCol, cellcontent, e) {
                var rowData = $('#table').getRowData(rowid);
            },
            shrinkToFit: true,
            autowidth:true,
            gridview: true,
            mtype: 'post',
            height: 'auto',
            rowNum: 10,
            rowList: [10, 20, 50, 100],
            pager: '#pager',
            emptyrecords: "暂无数据",
            sortname: 'id',
            viewrecords: true,
            rownumbers:true,
            //toolbar: [true,"top"],
            sortorder: "desc",
            caption: '评价列表&nbsp;&nbsp;<span style="color:orange;font-size: 10px;">双击行查看明细</span>'
        });
        $("#table") .jqGrid('setFrozenColumns')
        function search_on(){

            var url="<?php echo U('index');?>?"+$("form").serialize();
            jQuery("#table").jqGrid('setGridParam',{url:url,page:1}).trigger("reloadGrid");
        }
        function un_do(){
            $("form")[0].reset();
            jQuery("#table").jqGrid('setGridParam',{url:"<?php echo U('index');?>",page:1}).trigger("reloadGrid");
        }
        function excel(){
            var url_="<?php echo U('excel');?>?"+$("form").serialize();
            location.href=url_;
        }

    </script>
    </body>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>