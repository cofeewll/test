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
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-success pull-right">至今</span>
                        <h5>订单总额</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins"><?php echo ((isset($profit) && ($profit !== ""))?($profit):'0.00'); ?></h1>
                        <div class="stat-percent font-bold text-success">今日&nbsp;<?php echo ((isset($today_profit) && ($today_profit !== ""))?($today_profit):0.00); ?>&nbsp;<i class="fa fa-level-up"></i></div>
                        <small>总收入</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-info pull-right">至今</span>
                        <h5>订单数量</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins"><?php echo ($order_num); ?></h1>
                        <div class="stat-percent font-bold text-info">今日&nbsp;<?php echo ((isset($today_order_num) && ($today_order_num !== ""))?($today_order_num):0); ?>&nbsp;<i class="fa fa-level-up"></i></div>
                        <small>订单量</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-primary pull-right">至今</span>
                        <h5>商品数量</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins"><?php echo ($user_num); ?></h1>
                        <div class="stat-percent font-bold text-navy">今日&nbsp;<?php echo ((isset($today_user_num) && ($today_user_num !== ""))?($today_user_num):0); ?>&nbsp; <i class="fa fa-level-up"></i></div>
                        <small>商品量</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-danger pull-right">至今</span>
                        <h5>已提现金额</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins"><?php echo ($active_user); ?></h1>
                        <div class="stat-percent font-bold text-danger"><?php echo ($bl); ?>% <i class="fa fa-level-down"></i></div>
                        <small>总量</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>本月订单趋势图</h5>
                        <!--<div class="pull-right">-->
                            <!--<div class="btn-group">-->
                                <!--<button type="button" class="btn btn-xs btn-white active">天</button>-->
                                <!--<button type="button" class="btn btn-xs btn-white">月</button>-->
                                <!--<button type="button" class="btn btn-xs btn-white">年</button>-->
                            <!--</div>-->
                        <!--</div>-->
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="flot-chart">
                                    <div class="flot-chart-content" id="flot-dashboard-chart"></div>
                                </div>
                            </div>
                            <!--<div class="col-sm-3">-->
                                <!--<ul class="stat-list">-->
                                    <!--<li>-->
                                        <!--<h2 class="no-margins">2,346</h2>-->
                                        <!--<small>订单总数</small>-->
                                        <!--<div class="stat-percent">48% <i class="fa fa-level-up text-navy"></i>-->
                                        <!--</div>-->
                                        <!--<div class="progress progress-mini">-->
                                            <!--<div style="width: 48%;" class="progress-bar"></div>-->
                                        <!--</div>-->
                                    <!--</li>-->
                                    <!--<li>-->
                                        <!--<h2 class="no-margins ">4,422</h2>-->
                                        <!--<small>最近一个月订单</small>-->
                                        <!--<div class="stat-percent">60% <i class="fa fa-level-down text-navy"></i>-->
                                        <!--</div>-->
                                        <!--<div class="progress progress-mini">-->
                                            <!--<div style="width: 60%;" class="progress-bar"></div>-->
                                        <!--</div>-->
                                    <!--</li>-->
                                    <!--<li>-->
                                        <!--<h2 class="no-margins ">9,180</h2>-->
                                        <!--<small>最近一个月销售额</small>-->
                                        <!--<div class="stat-percent">22% <i class="fa fa-bolt text-navy"></i>-->
                                        <!--</div>-->
                                        <!--<div class="progress progress-mini">-->
                                            <!--<div style="width: 22%;" class="progress-bar"></div>-->
                                        <!--</div>-->
                                    <!--</li>-->
                                    <!--</ul>-->
                            <!--</div>-->
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <div class="row">



                </div>
            </div>
        </div>
    </div>

    <!--<script src="js/jquery.min.js?v=2.1.4"></script>-->
    <!--<script src="js/bootstrap.min.js?v=3.3.6"></script>-->
    <script src="/Public/Admin/js/plugins/flot/jquery.flot.js"></script>
    <script src="/Public/Admin/js/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="/Public/Admin/js/plugins/flot/jquery.flot.spline.js"></script>
    <script src="/Public/Admin/js/plugins/flot/jquery.flot.resize.js"></script>
    <script src="/Public/Admin/js/plugins/flot/jquery.flot.pie.js"></script>
    <script src="/Public/Admin/js/plugins/flot/jquery.flot.symbol.js"></script>
    <script src="/Public/Admin/js/plugins/peity/jquery.peity.min.js"></script>
    <script src="/Public/Admin/js/demo/peity-demo.min.js"></script>
    <!-- <script src="__JS__/content.min.js?v=1.0.0"></script> -->
    <script src="/Public/Admin/js/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="/Public/Admin/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="/Public/Admin/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="/Public/Admin/js/plugins/easypiechart/jquery.easypiechart.js"></script>
    <script src="/Public/Admin/js/plugins/sparkline/jquery.sparkline.min.js"></script>
    <script src="/Public/Admin/js/demo/sparkline-demo.min.js"></script>
    <script>
         $(document).ready(
                 function(){
                     $(".chart").easyPieChart({barColor:"#f8ac59",scaleLength:5,lineWidth:4,size:80});
                     $(".chart2").easyPieChart({barColor:"#1c84c6",scaleLength:5,lineWidth:4,size:80});
                     var date=<?php echo ($date); ?>;
                     var data2=[],data3=[];
                     var arr=<?php echo ($arr); ?>,money=<?php echo ($money); ?>;
                     for(var i=1;i<=date;i++){
                         var arr1=[];
                         arr1[0]=gd(<?php echo ($year); ?>,<?php echo ($month); ?>,i);
                         arr1[1]=arr[i-1];
                         data2.push(arr1);

                         var arr2=[];
                         arr2[0]=gd(<?php echo ($year); ?>,<?php echo ($month); ?>,i);
                         arr2[1]=money[i-1];
                         data3.push(arr2);
                     }
 //                    console.log(data2)

                     var dataset=[
                         {
                             label:"订单数",
                             data:data2,
                             color:"#1ab394",
                             bars:{show:true,align:"center",barWidth:24*60*60*600,lineWidth:0}
                         },
                         {
                             label:"付款额",
                             data:data3,
                             yaxis:2,
                             color:"#464f88",
                             lines:{
                                 lineWidth:1,
                                 show:true,

                                 fill:true,
                                 fillColor:{colors:[{opacity:0.2},{opacity:0.2}]}
                             },
                             splines:{show:false,tension:0.6,lineWidth:1,fill:0.1},
                         }];
                     var options={
                         xaxis:{
                             mode:"time",
                             tickSize:[3,"day"],
                             tickLength:0,
                             axisLabel:"Date",
                             axisLabelUseCanvas:true,
                             axisLabelFontSizePixels:12,
                             axisLabelFontFamily:"Arial",
                             axisLabelPadding:10,
                             color:"#838383"
                         },
                         yaxes:[
                             {
                                 position:"left",
 //                                max:100,
                                 color:"#838383",
                                 axisLabelUseCanvas:true,
                                 axisLabelFontSizePixels:12,
                                 axisLabelFontFamily:"Arial",
                                 axisLabelPadding:3
                             },
                             {
                                 position:"right",
                                 clolor:"#838383",
                                 axisLabelUseCanvas:true,
                                 axisLabelFontSizePixels:12,
                                 axisLabelFontFamily:" Arial",
                                 axisLabelPadding:67
                             }
                         ],
                         legend:{noColumns:1,labelBoxBorderColor:"#000000",position:"nw"},
                         grid:{hoverable:false,borderWidth:0,color:"#838383"}
                     };
                     function gd(year,month,day){
                         return new Date(year,month-1,day).getTime()
                     }
                     var previousPoint=null,previousLabel=null;
                     $.plot($("#flot-dashboard-chart"),dataset,options);
                     var mapData={"US":298,"SA":200,"DE":220,"FR":540,"CN":120,"AU":760,"BR":550,"IN":200,"GB":120,};
                     $("#world-map").vectorMap({map:"world_mill_en",backgroundColor:"transparent",regionStyle:{initial:{fill:"#e4e4e4","fill-opacity":0.9,stroke:"none","stroke-width":0,"stroke-opacity":0}},series:{regions:[{values:mapData,scale:["#1ab394","#22d6b1"],normalizeFunction:"polynomial"}]},})});
    </script>
    </body>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>