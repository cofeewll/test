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
    <script src="/Public/Admin/js/jqgrid.js"></script>
    <script>
        var jqgrid_coloumn=[
            {label: '类型', name: 'type', index: 'type', align: 'center',formatter:function(a,b,c){
                if(a==1)return "订单获取";
                if(a==2)return "转盘消费";
                if(a==3)return "转赠好友";
                if(a==4)return "管理员修改";
                if(a==5)return "好友赠送";
            }},
            {label: '金币量', name: 'cscore', index: 'cscore',  align: 'center',},
            {label: '变化前数量',  name: 'beforeScore', index: 'beforeScore', align: 'center',},
            {label: '变化后数量', name: 'afterScore', index: 'afterScore',  align: 'center',},
            {label: '备注', name: 'memo', index: 'memo',  align: 'center',},
            {label: '创建时间',name: 'createTime', index: 'createTime',  align: 'center', width:'200'},

        ];
        var caption='积分明细记录';
        var reurl="<?php echo U('score','','');?>/uid/"+<?php echo ($uid); ?>;
        var loadComplete=function(){};
        var ondblClickRow=function(id){
        };
        var onCellSelect=function(rowid, iCol, cellcontent, e){
        };
        var footerrow=false;
        getGrid(jqgrid_coloumn,loadComplete,ondblClickRow,onCellSelect,"id",caption,reurl,footerrow);
        //
    </script>
    </body>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>