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

            {label: '提现金额', name: 'setMoney', index: 'setMoney',  align: 'center',},
            {label: '提现账户', name: 'type', index: 'type',  align: 'center',formatter:function(a,b,c){
                if(a==1)return "支付宝";
                if(a==2)return "微信";
                if(a==3)return "银行卡";
            }},
            {label: '账号', name: 'account', index: 'account',  align: 'center',},
            {label: '手续费', name: 'fee', index: 'fee',  align: 'center',},
            {label: '状态', name: 'status', index: 'status',  align: 'center',formatter:function(a,b,c){
                if(a==0)return "待审核";
                if(a==1)return "同意提现";
                if(a==2)return "拒绝提现";
            }},
            {label: '备注', name: 'remark', index: 'remark',  align: 'center',},
            {label: '提现时间', name: 'addTime', index: 'addTime',  align: 'center',},

        ];
        var caption='<a  href="###" onclick="add_()"> <i class="fa fa-plus"></i>申请提现 </a>';
        var reurl="<?php echo U('withdraw');?>";
        var loadComplete=function(){};
        var ondblClickRow=function(id){
            var rowData = $('#table').getRowData(id);
            layer.open({
                type: 2,
                title: '查看详细',
                shadeClose: true,
                shade: 0.8,
                area: ['1200px', '90%'],
                content: "<?php echo U('sdetail','','');?>/id/"+rowData.id,
                end:function(){
                    $("#table").trigger("reloadGrid");
                }
            });
        };
        var onCellSelect=function(rowid, iCol, cellcontent, e){
        };
        var footerrow=false;
        getGrid(jqgrid_coloumn,loadComplete,ondblClickRow,onCellSelect,"id",caption,reurl,footerrow);
        function add_(){
            layer.open({
                type: 2,
                title: '申请提现',
                shadeClose: true,
                shade: 0.8,
                area: ['1200px', '90%'],
                content: "<?php echo U('apply','','');?>",
                end:function(){
                    $("#table").trigger("reloadGrid");
                }
            });
        }
    </script>
    </body>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>