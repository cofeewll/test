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
                                <label  class="sr-only">手机号</label>
                                <input type="text" placeholder="请输入用户手机号"  class="form-control" name="phone">
                            </div>
                            <div class="form-group">
                                <label  class="sr-only">用户编号</label>
                                <input type="text" placeholder="请输入用户编号"  class="form-control" name="number">
                            </div>
                            <div class="form-group">
                                <select name="uStatus" class="form-control">
                                    <option value="">请选择用户状态</option>
                                    <option value="1">正常</option>
                                    <option value="0">禁用</option>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="m-t-none m-b">添加用户</h3>

                            <form role="form1">
                                <div class="form-group">
                                    <label>手机号：</label>
                                    <input type="text" placeholder="请输入手机号" class="form-control" name="phone">
                                </div>
                                <div class="form-group">
                                    <label>密码：</label>
                                    <input type="text" placeholder="请输入密码" class="form-control" name="password">
                                </div>
                                <div class="form-group">
                                    <label>性别：</label>
                                    <input type="radio" name="sex" value="1" checked>男
                                    <input type="radio" name="sex" value="2">女
                                </div>
                                <div>
                                    <span class="btn  btn-primary  m-t-n-xs" type="submit" id="submit"><strong>添加</strong>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-form1" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="width:400px;">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="m-t-none m-b" id="show_text"></h3>
                            <div style="margin-left:50px;">
                                <img id="code_img">
                                <div style="margin-left:40px;">分享二维码扫码注册</div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a data-toggle="modal" href="#modal-form1" style="display:none;" id="qrcode"> </a>
    </div>
    <script src="/Public/Admin/js/plugins/layer/laydate/laydate.js"></script>
    <script src="/Public/Admin/js/public.js"></script>
    <script src="/Public/Admin/js/jqgrid.js"></script>
    <script>
        var jqgrid_coloumn=[
            {label: '二维码', align: 'center',width:"100",formatter:function(a,b,c){
                return '<a ><span class="glyphicon glyphicon-qrcode"></span></a>';
            }},
            {label: '手机号', name: 'phone', index: 'phone', align: 'center',},
            {label: '用户编号', name: 'number', index: 'number',  align: 'center',},
            {label: '钱包',  name: 'money', index: 'money', align: 'center',formatter:function(a,b,c){
                return "<a><span>"+a+"</span></a>"
            }},
            {label: '积分', name: 'score', index: 'score',  align: 'center',formatter:function(a,b,c){
                return "<a><span>"+a+"</span></a>"
            }},
            {label: '金币', name: 'gold', index: 'gold',  align: 'center',formatter:function(a,b,c){
                return "<a><span>"+a+"</span></a>"
            }},
            {label: '昵称',name: 'nickname', index: 'nickname', align: 'center', },
            {label: '状态',name: 'status', index: 'status', align: 'center',width:'80',formatter: function (a, b, c) {
                if (a == 0)
                    return '<a><i class="fa fa-close" style="color:red"></i></a>';
                if (a == 1)
                    return '<a><i class="fa fa-check" style="color: #00a157"></i></a>';
            }},

            {label: '上级编号',name: 'pnumber', index: 'pnumber', align: 'center', },
            {label: '最后登录时间',name: 'loginTime', index: 'loginTime', align: 'center', width:'200'},
            {label: '创建时间',name: 'createTime', index: 'createTime',  align: 'center', width:'200'},
            {label: 'id', name: 'id', index: 'id', align: 'center',hidden:true },
            {label: 'qcode', name: 'qcode', index: 'qcode', align: 'center',hidden:true },
//            {label: '操作', align: 'center', width:'250',formatter: function (a, b, c) {
//                var str="<a  onclick='getMoney(" + c.id + ")'  href='javascript:;' class='btn-xs btn-primary'>钱包<i class='fa fa-search'></i></a>";
//                str+="&nbsp;&nbsp;<a  onclick='getScore(" + c.id + ")'  href='javascript:;' class='btn-xs btn-danger'>积分<i class='fa fa-search'></i></a>";
//                str+="&nbsp;&nbsp;<a  onclick='getGold(" + c.id + ")'  href='javascript:;' class='btn-xs btn-info'>金币<i class='fa fa-search'></i></a>";
//                return str;   }},

        ];
        var caption='<a data-toggle="modal" href="#modal-form"><i class="fa fa-plus"></i>添加 </a>';
        var reurl="<?php echo U('index');?>";
        var loadComplete=function(){};
        var ondblClickRow=function(id){
            var rowData = $('#table').getRowData(id);
            layer.open({
                type: 2,
                title: '查看用户详情',
                shadeClose: true,
                shade: 0.8,
                area: ['90%', '90%'],
                content: "<?php echo U('getMoney','','');?>/id/"+rowData.id
            });
        };
        var onCellSelect=function(rowid, iCol, cellcontent, e){
            var rowData = $('#table').getRowData(rowid);
            if (iCol ==8 ) {
                $.post("<?php echo U('check_status');?>",{id:rowData.id}, function (dat) {
                    if (dat.status == 0) {
                        layer.alert(dat.msg);
                    } else {
                        jQuery('#table').jqGrid('setCell', rowid, dat.colname, dat.data);
                    }
                });
            }
            if (iCol ==1 ) {
                $("#qrcode").click();
                $("#show_text").html("用户编号："+rowData.number);
                $("#code_img").attr("src",rowData.qcode);
            }
            if (iCol ==5 ) {
                layer.open({
                    type: 2,
                    title: '查看用户积分',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['90%', '90%'],
                    content: "<?php echo U('score','','');?>/uid/"+rowData.id
                });
            }
            if (iCol ==4 ) {
                layer.open({
                    type: 2,
                    title: '查看用户余额',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['90%', '90%'],
                    content: "<?php echo U('money','','');?>/uid/"+rowData.id
                });
            }
            if (iCol ==6 ) {
                layer.open({
                    type: 2,
                    title: '查看用户金币',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['90%', '90%'],
                    content: "<?php echo U('gold','','');?>/uid/"+rowData.id
                });
            }
        };
        var footerrow=false;
        getGrid(jqgrid_coloumn,loadComplete,ondblClickRow,onCellSelect,"id",caption,reurl,footerrow);
        //

        $("#submit").click(function(){
            var phone=$("form[role=form1]").find("input[name=phone]").val();
            var password=$("form[role=form1]").find("input[name=password]").val();
            if(phone==''){
                layer.msg("请输入手机号");return ;
            }
            if(password==''){
                layer.msg("请输入密码");return ;
            }
            $(this).attr("id","");
            $.post("<?php echo U('add');?>",$("form[role=form1]").serialize(),function(ret){
                layer.msg(ret.msg);
                if(ret.status==1){
                    $(this).attr("id","submit");
                    $("form[role=form1]")[0].reset();
                    jQuery("#table").jqGrid().trigger("reloadGrid");
                }

            })
        })
    </script>
    </body>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>