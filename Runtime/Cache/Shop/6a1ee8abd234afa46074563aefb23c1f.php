<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    
    <!--<title>唯公商城-管理主页</title>-->


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
                                <input class="form-control" name="uNumber" placeholder="请输入用户编号">
                            </div>

                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">请选择订单状态</option>
                                    <option value="0">待支付</option>
                                    <option value="1">待发货</option>
                                    <option value="2">待收货</option>
                                    <option value="3">退款中</option>
                                    <option value="4">交易关闭</option>
                                    <option value="5">交易成功</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="payTime" class="form-control">
                                    <option value="">请选择支付状态</option>
                                    <option value="0">未支付</option>
                                    <option value="1">已支付</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="payType" class="form-control">
                                    <option value="">请选择支付方式</option>
                                    <option value="1">支付宝</option>
                                    <option value="2">微信</option>
                                    <option value="0">余额支付</option>
                                </select>
                            </div>

                            <div class="form-group" style="margin-top: 3px;">
                                <!--<div class="col-sm-10">-->
                                <input placeholder="开始日期" class="form-control layer-date" id="start" name="start">
                                <input placeholder="结束日期" class="form-control layer-date" id="end" name="end">
                                <!--</div>-->
                            </div>
                            <button class="btn-sm btn-primary " type="button"><i class="fa fa-search" onclick="search_on()"></i></button>
                            <button class="btn-sm btn-info " type="button"><i class="fa fa-undo" onclick="un_do()"></i></button>
                            <button class="btn-sm btn-warning " type="button"><i class="fa fa-download" onclick="excel()"></i></button>
                            <!--<button class="btn-sm btn-danger " type="button" style="margin-top: 10px;"><i class="fa fa-truck" onclick="excel()"></i>&nbsp;&nbsp;发货</button>-->
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
    <div id="modal-form" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" >
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="m-t-none m-b" id="show_text"></h3>
                            <table class="table table-bordered">
                                <tr><td>订单总额</td><td></td></tr>
                                <tr><td>收货人</td><td></td></tr>
                                <tr><td>收货地址</td><td></td></tr>
                                <tr><td>联系电话</td><td></td></tr>
                                <tr><td>运费</td><td></td></tr>
                            </table>
                            <form role="form1">
                                <div class="form-group">
                                    <label>请选择快递公司：</label>
                                    <select class="form-control" name="shipName">
                                        <option value="">请选择</option>
                                        <?php if(is_array($ship)): $i = 0; $__LIST__ = $ship;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($v); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>输入快递单号：</label>
                                    <input type="text" placeholder="请输入快递单号" class="form-control" name="shipId">
                                    <input type="hidden"  class="form-control" name="id">
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
        <a data-toggle="modal" href="#modal-form" style="display:none;" id="ship"> </a>
    </div>
    <div id="modal-form1" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" >
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="m-t-none m-b" id="show_text1"></h3>
                            <form role="form2">
                                <div class="form-group">
                                    <label>请选择快递公司：</label>
                                    <select class="form-control" name="shipName">
                                        <option value="">请选择</option>
                                        <?php if(is_array($ship)): $i = 0; $__LIST__ = $ship;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($v); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                    <input type="hidden"  class="form-control" name="id">
                                </div>
                                <div>
                                    <span class="btn  btn-primary  m-t-n-xs" type="submit" id="submit1"><strong>打印</strong>
                                    </span>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a data-toggle="modal" href="#modal-form1" style="display:none;" id="print_2"> </a>
    </div>
    <div style="display:none;" id="print_1"></div>
    <script src="/Public/Admin/js/plugins/layer/laydate/laydate.js"></script>
    <script src="/Public/Admin/js/public.js"></script>
    <script src="/Public/Admin/js/ship.js"></script>
    <script src="/Public/Admin/js/print.js"></script>
    <script>
        $('#table').jqGrid({
            url: "<?php echo U('index');?>",
            datatype: "json",
            colModel: [
//                {label: '全选', align: 'center',width:"50",formatter:function(a,b,c){
//                    return '<input type="checkbox" value="'+c.id+'" name="child">'
//                }},
                {label: '物流', align: 'center',width:"50",formatter:function(a,b,c){
                    if(c.status>1&&c.status!=4){
                        return '<a onclick="ship('+c.id+')"><i class="fa fa-truck"></i></a>';
                    }else{
                        return "";
                    }
                }},
                {label: '订单编号', name: 'orderSn', index: 'orderSn',  align: 'center',width:"150",},
                {label: '下单时间', name: 'createTime', index: 'createTime', width: 150, align: 'center', },
                {label: '用户编号', name: 'number', index: 'number',  align: 'center',width:"60"},
                {label: '订单状态', name: 'status', index: 'status', width: 80, align: 'center',formatter: function (a, b, c) {
                    if (a == 0)return '<span class="label label-default">待支付</span>';
                    if (a == 1)return '<span class="label label-success">待发货</span>';
                    if (a == 2)return '<span class="label label-info">待收货</span>';
                    if (a == 3)return '<span class="label label-danger">退款中</span>';
                    if (a == 4)return '<span class="label label-warning">交易关闭</span>';
                    if (a == 5)return '<span class="label label-primary">交易成功</span>';
                } },
                {label: '支付状态',name: 'payTime', index: 'payTime',  align: 'center',width:"80",formatter:function(a,b,c){
                    if(a>0){
                        return '<span class="label label-primary"><i class="fa fa-check"></i></span>';
                    }else{
                        return '<span class="label label-default"><i class="fa fa-close"></i></span>';
                    }
                }},
                {label: '订单总额',name: 'amount', index: 'amount',  align: 'center',width:"80"},
                {label: '商品总额',  name: 'goodsAmount', index: 'goodsAmount',  align: 'center',width:"80" },

                {label: '支付方式',  name: 'payType', index: 'payType', width:"70",align: 'center',formatter: function (a, b, c) {
                    if(c.payTime==0){
                        return "";
                    }else{
                        if (a == 1)return '支付宝';
                        if (a == 2)return '微信';
                        if(a==0)return "余额";
                    }
                } },

                {label: '配送区域',  name: 'address', index: 'address',  align: 'center',width:"200" },
                {label: '评价', name: 'isComment', index: 'isComment', width: 50, align: 'center', formatter:function(a,b,c){
                    if (a == 0)
                        return '<i class="fa fa-close" style="color:red"></i>';
                    if (a == 1)
                        return '<i class="fa fa-check" style="color: #00a157"></i>';
                }},
                {label: '快递公司', name: 'shipName', index: 'shipName', width: 80, align: 'center', },
                {label: '物流单号', name: 'shipId', index: 'shipId', width: 150, align: 'center', },

                { name: 'id', index: 'id', hidden:true},
                {name: 'total_oMoney', index: 'total_oMoney',hidden:true},
                { name: 'total_oPayMoney', index: 'total_oPayMoney', hidden:true},
                { name: 'order_status', index: 'order_status', hidden:true},
                { name: 'fee', index: 'fee', hidden:true},
                { name: 'address', index: 'address', hidden:true},
                { name: 'realname', index: 'realname', hidden:true},
                { name: 'phone', index: 'phone', hidden:true},
            ],
            ondblClickRow: function(id){
                var rowData = $('#table').getRowData(id);
                layer.open({
                    type: 2,
                    title: '查看订单详细',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['1020px', '90%'],
                    content: "<?php echo U('detail','','');?>/id/"+rowData.id,
                });

            },
            loadComplete: function () {
                var grid = $("#table");
                var rowNum = $(this).jqGrid('getGridParam', 'records');
                if (rowNum >0) {
                    var strIds=  grid.jqGrid("getDataIDs");//获得表格所有行的ID
                    var rowData =  grid.jqGrid("getRowData",strIds[0]);
                    var oMoney=rowData.total_oMoney;
                    var oPayMoney=rowData.total_oPayMoney;
                    grid.footerData("set", {orderSn: "合计", amount: oMoney,goodsAmount:oPayMoney});
                }else{
                    grid.footerData("set", {orderSn: "合计", amount: 0.00,goodsAmount:0.00});
                }
            },
            footerrow: true,
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
            caption: '<button class="btn-xs btn-danger " onclick="send()"><i class="fa fa-truck" ></i>发货</button>'+
                    '&nbsp;&nbsp;<button class="btn-xs btn-warning " onclick="print_()"><i class="fa fa-print" ></i>打印电子面单</button>'
        });

        function ship(id){
            layer.open({
                type: 2,
                title: '查看物流信息',
                shadeClose: true,
                shade: 0.8,
                area: ['1020px', '90%'],
                content: "<?php echo U('ship','','');?>/id/"+id,
            });
        }

    </script>
    </body>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>