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

    <style type="text/css">
        .SelectBG{
            background-color:orange;
        }
    </style>
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
                                <select name="status" class="form-control">
                                    <option value="">请选择订单状态</option>
                                    <option value="-1">撤回申请</option>
                                    <option value="0">待处理</option>
                                    <option value="1">同意退款</option>
                                    <option value="2">拒绝退款</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <!--<div class="col-sm-10">-->
                                <input placeholder="开始日期" class="form-control layer-date" id="start" name="start">
                                <input placeholder="结束日期" class="form-control layer-date" id="end" name="end">
                                <!--</div>-->
                            </div>
                            <button class="btn-sm btn-primary " type="button"><i class="fa fa-search" onclick="search_on()"></i></button>
                            <button class="btn-sm btn-info " type="button"><i class="fa fa-undo" onclick="un_do()"></i></button>
                            <button class="btn-sm btn-warning " type="button"><i class="fa fa-download" onclick="excel()"></i></button>
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
            url: "<?php echo U('refund');?>",
            datatype: "json",
            colModel: [
                {label: '订单编号', name: 'orderSn', index: 'orderSn',  align: 'center',},
                {label: '状态', name: 'status', index: 'status', width: 100, align: 'center', formatter:function(a,b,c){
                    if (a == -1)return '<span class="label label-default">撤回申请</span>';
                    if (a == 0)return '<span class="label label-warning">待处理</span>';
                    if (a == 1)return '<span class="label label-primary">同意退款</span>';
                    if (a == 2)return '<span class="label label-danger">拒绝退款</span>';
                }},
                {label: '商品名称', name: 'name', index: 'name',  align: 'center',width:'200'},
                {label: '商品规格', name: 'spec_key_name', index: 'spec_key_name',  align: 'center',},
                {label: '退款金额',  name: 'amount', index: 'amount',  align: 'center',width:"100" },
                {label: '实退金额',  name: 'realMoney', index: 'realMoney',  align: 'center',width:"100" },
                {label: '补偿金',  name: 'addMoney', index: 'addMoney',  align: 'center',width:"100" },


                {label: '退款原因',name: 'reason', index: 'reason',  align: 'center',},


                {label: '创建时间', name: 'createTime', index: 'createTime', width: 150, align: 'center', },
                { name: 'id', index: 'id', hidden:true},
                { name: 'warn', index: 'warn', hidden:true},
            ],
            ondblClickRow: function(id){
                var rowData = $('#table').getRowData(id);
                layer.open({
                    type: 2,
                    title: '查看详细',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['1020px', '90%'],
                    content: "<?php echo U('rdetail','','');?>/id/"+rowData.id,
                    end:function(){
                        $("#table").trigger("reloadGrid");
                    }
                });

            },
            loadComplete: function () {
                var ids = $("#table").getDataIDs();
                for(var i=0;i<ids.length;i++){
                    var rowData = $("#table").getRowData(ids[i]);
                    if(rowData.warn==1){
                        //如果天数等于0，则背景色置灰显示
                        $('#'+ids[i]).find("td").addClass("SelectBG");
                    }
                }

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
            caption: '售后申请列表&nbsp;&nbsp;<span style="color:orange;font-size: 10px;">双击行查看明细</span>'
        });
        $("#table") .jqGrid('setFrozenColumns')
        function search_on(){

            var url="<?php echo U('refund');?>?"+$("form").serialize();
            jQuery("#table").jqGrid('setGridParam',{url:url,page:1}).trigger("reloadGrid");
        }
        function un_do(){
            $("form")[0].reset();
            jQuery("#table").jqGrid('setGridParam',{url:"<?php echo U('refund');?>",page:1}).trigger("reloadGrid");
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