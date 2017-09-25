<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    
    <title>咭咭生活-管理主页</title>


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
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true"> 订单金额统计</a>
                        </li>
                        <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">按年月统计</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <div class="panel-body">
                                <div class="ibox ">
                                    <div class="ibox-content">
                                        <form role="form1" class="form-inline">
                                            <div class="form-group">
                                                <select name="status" class="form-control">
                                                    <option value="">请选择订单状态</option>
                                                    <option value="1">待发货</option>
                                                    <option value="2">待收货</option>
                                                    <option value="3">退款中</option>
                                                    <option value="4">交易关闭</option>
                                                    <option value="5">交易成功</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <!--<div class="col-sm-10">-->
                                                <input placeholder="开始日期" class="form-control layer-date" id="start" name="start">
                                                <input placeholder="结束日期" class="form-control layer-date" id="end" name="end">
                                                <!--</div>-->
                                            </div>
                                            <button class="btn-sm btn-primary " type="button"><i class="fa fa-search" onclick="search_on()"></i></button>
                                            <!--<button class="btn-sm btn-info " type="button"><i class="fa fa-undo" onclick="un_do()"></i></button>-->
                                        </form>

                                    </div>

                                    <div class="col-sm-6" >
                                        <div class="ibox float-e-margins" >
                                            <div class="ibox-title">
                                                <h5>按照订单支付方式统计</h5>
                                            </div>
                                            <div class="ibox-content" style="height:275px;">
                                                <table class="table table-bordered">
                                                    <thead >
                                                    <tr >
                                                        <td>余额支付</td>
                                                        <td>支付宝</td>
                                                        <td>微信</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="order">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="ibox float-e-margins">
                                            <div class="ibox-title">
                                                <h5>订单来源饼状图</h5>
                                            </div>
                                            <div class="ibox-content" style="overflow: auto;">
                                                <div class="echarts" id="echarts-pie-chart"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="ibox float-e-margins">
                                            <div class="ibox-title">
                                                <h5>按照商品分类统计</h5>
                                            </div>
                                            <div class="ibox-content" style="height:275px;overflow: auto;">
                                                <table class="table table-bordered">
                                                    <thead >
                                                    <tr >
                                                        <td>分类名称</td>
                                                        <td>累计金额</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="school">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="ibox float-e-margins">
                                            <div class="ibox-title">
                                                <h5>商品分类饼状图</h5>
                                            </div>
                                            <div class="ibox-content" style="overflow: auto;">
                                                <div class="echarts" id="echarts-pie-chart1"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div id="tab-2" class="tab-pane">
                            <div class="panel-body">
                                <div class="ibox ">
                                    <div class="ibox-content">
                                        <form role="form2" class="form-inline">
                                            <div class="form-group">
                                                <select name="status" class="form-control">
                                                    <option value="">请选择订单状态</option>
                                                    <option value="1">待发货</option>
                                                    <option value="2">待收货</option>
                                                    <option value="3">退款中</option>
                                                    <option value="4">交易关闭</option>
                                                    <option value="5">交易成功</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <select class="form-control" name="year">
                                                    <option value="1">按年统计</option>
                                                    <option value="2">按月统计</option>
                                                </select>
                                            </div>
                                            <div class="form-group" style="display:none;">
                                                <!--<div class="col-sm-10">-->
                                                <input placeholder="开始日期" class="form-control layer-date" id="start1" name="start" style="">
                                                <input placeholder="结束日期" class="form-control layer-date" id="end1" name="end">
                                                <!--</div>-->
                                            </div>
                                            <button class="btn-sm btn-primary " type="button"><i class="fa fa-search" onclick="search_on1()"></i></button>
                                            <!--<button class="btn-sm btn-info " type="button"><i class="fa fa-undo" onclick="un_do()"></i></button>-->
                                        </form>

                                    </div>

                                    <div class="col-sm-12" style="display:none;" id="statis_year">
                                        <div class="ibox float-e-margins">
                                            <div class="ibox-title">
                                                <h5>年统计柱状图</h5>
                                            </div>
                                            <div class="ibox-content" style="overflow: auto;">
                                                <div class="echarts" id="echarts-bar-chart"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12" style="display:none;" id="statis_month">
                                        <div class="ibox float-e-margins">
                                            <div class="ibox-title">
                                                <h5>月统计柱状图</h5>
                                            </div>
                                            <div class="ibox-content" style="overflow: auto;">
                                                <div class="echarts" id="echarts-bar-chart1"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>

    <script src="/Public/Admin/js/plugins/layer/laydate/laydate.js"></script>
    <script src="/Public/Admin/js/plugins/echarts/echarts-all.js"></script>
    <script src="/Public/Admin/js/statis.js"></script>
    <script>
        var start={
            elem:"#start",
            format:"YYYY-MM-DD hh:mm:ss",
            max:"2099-06-16 23:59:59",istime:true,istoday:true,
            choose:function(datas){
                end.min=datas;
                end.start=datas
            }};
        var end={
            elem:"#end",
            format:"YYYY-MM-DD hh:mm:ss",
            min:laydate.now(),
            max:"2099-06-16 23:59:59",
            istime:true,istoday:true,
            choose:function(datas){
                start.max=datas
            }};
        laydate(start);
        laydate(end);
        $("select[name=year]").change(function(){
            $(this).parent().next("div").css({display:"none"});
            if($(this).val()==2){
                $(this).parent().next("div").css({display:"inline"});
            }
        });

    </script>
    </body>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>