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
                                <label for="phone" class="sr-only">手机号</label>
                                <input type="text" placeholder="请输入用户手机号" id="phone" class="form-control" name="phone">
                            </div>
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">请选择处理状态</option>
                                    <option value="0">待审核</option>
                                    <option value="1">审核成功</option>
                                    <option value="2">拒绝请求</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <!--<div class="col-sm-10">-->
                                <input placeholder="开始日期" class="form-control layer-date" id="start" name="start">
                                <input placeholder="结束日期" class="form-control layer-date" id="end" name="end">
                                <!--</div>-->
                            </div>
                            <button class="btn-sm btn-primary " type="button"><i class="fa fa-search" onclick="search('index')"></i></button>
                            <button class="btn-sm btn-info " type="button"><i class="fa fa-undo" onclick="un_do('index')"></i></button>
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
        <div class="modal-dialog money">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <form role="form1">
                                <div class="form-group">
                                    <label>审核：</label>
                                    <input type="radio" name="status" value="1" checked>同意充值
                                    <input type="radio" name="status" value="2">拒绝充值
                                </div>
                                <div>
                                    <input type="hidden" name="id">
                                </div>
                            </form>
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
            {label: '手机号', name: 'phone', index: 'phone', align: 'center',},
            {label: '用户编号', name: 'number', index: 'number',  align: 'center',search: false,},
            {label: '改变余额',  name: 'cmoney', index: 'cmoney', align: 'center',search: false,formatter:function(a,b,c){
                if(c.is_add==0)return "<span style='color:red;'>-"+a+"</span>";
                if(c.is_add==1)return "<span style='color:green;'>+"+a+"</span>";
            }},
            {label: '备注', name: 'memo', index: 'memo',  align: 'center',search: false,},
            {label: '发起人', name: 'username', index: 'username',  align: 'center',search: false,},
            {label: '状态',name: 'status', index: 'status', align: 'center',formatter: function (a, b, c) {
                if (a == 0)return '<span style="color:deepskyblue">待审核</span>';
                if (a == 1)return '<span style="color:green">审核成功</span>';
                if (a == 2)return '<span style="color:red">拒绝请求</span>';
            }},
            {label: '创建时间',name: 'createTime', index: 'createTime',  align: 'center', width:'200'},
            {label: 'id', name: 'id', index: 'id', align: 'center',hidden:true },
            {label: '操作', align: 'center', formatter: function (a, b, c) {
                return "<a  onclick='submit1("+c.id+")' title='查看' href='###'><i class='fa fa-edit'></i></a>";   }},

        ];
        var caption='充值申请列表';
        var reurl="<?php echo U('index');?>";
        var loadComplete=function(){};
        var ondblClickRow=function(id){
        };
        var onCellSelect=function(rowid, iCol, cellcontent, e){
        };
        var footerrow=false;
        getGrid(jqgrid_coloumn,loadComplete,ondblClickRow,onCellSelect,"id",caption,reurl,footerrow);
        //
        function submit1(id){
            layer.confirm($("div.money").html(), {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.post("<?php echo U('check');?>",$('form[role="form1"]').serialize(),function(data){
                    layer.msg(data.msg);
                    if(data.status==1){
                        window.location.reload();
                    }
                })
            }, function(){

            });
            $("input[name=id]").val(id);
            $("div.layui-layer-dialog").css({width:"400px"});
        }
    </script>
    </body>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>