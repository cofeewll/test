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
    <div id="modal-form" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="m-t-none m-b">推送</h3>

                            <form role="form">
                                <div class="form-group">
                                    <label>选择角色：</label>
                                    <input type="checkbox"  name="role[]" value="1">普通用户
                                    <input type="checkbox"  name="role[]" value="2">服务商
                                </div>
                                <input type="hidden" name="nId">
                                <div>
                                    <span class="btn  btn-primary  m-t-n-xs" type="submit" id="submit"><strong>确定</strong>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a class="btn-sm btn-white btn-bitbucket" data-toggle="modal" href="#modal-form" style="display:none;" id="send_data"> <i class="fa fa-plus"></i>添加 </a>
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
                {label: '序号', name: 'id', index: 'id', width: 50, align: 'center',hidden:true},

                {label: '标题',  name: 'title', index: 'title', width: 400, align: 'center',},
                {label: '推送',  name: 'isPush', index: 'isPush', width: 100, align: 'center',formatter: function (a, b, c) {
                    if (a == 0)
                        return '<i class="fa fa-close" style="color:red"></i>';
                    if (a == 1)
                        return '<i class="fa fa-check" style="color: #00a157"></i>';
                }},
                {label: '状态',  name: 'status', index: 'status', width: 100, align: 'center',formatter: function (a, b, c) {
                    if (a == 0)
                        return '<a><i class="fa fa-close" style="color:red"></i></a>';
                    if (a == 1)
                        return '<a><i class="fa fa-check" style="color: #00a157"></i></a>';
                }},
                {label: '查看角色', name: 'type', index: 'type', width: 200, align: 'center', formatter:function(a,b,c){
                    if(a==0)return "全部";
                    if(a==1)return "用户";
                    if(a==2)return "商家";
                }},
                {label: '创建时间', editable: false, search: false, name: 'createTime', index: 'createTime', width: 200, align: 'center', },
                {label: '操作', editable: true, search: false, width: 200, align: 'center', formatter: function (a, b, c) {
                    var str="<a  onclick='set_edit(" + c.id + ")'  href='javascript:;'><i class='fa fa-edit'></i></a>";
                    str+="&nbsp;&nbsp;&nbsp;";
                    str+="<a  onclick='del_data(" + c.id + ")'  href='javascript:;'><i class='fa fa-trash'></i></a>";
                    str+="&nbsp;&nbsp;&nbsp;";
                    str+="<a  onclick='send(" + c.id + ")'  href='javascript:;' title='推送'><i class='fa fa-comment'></i></a>";

                    return str;
                }},

            ],
            onCellSelect: function (rowid, iCol, cellcontent, e) {
                var rowData = $('#table').getRowData(rowid);
                if (iCol ==4) {
                    $.post("<?php echo U('check_status');?>",{uId:rowData.nId}, function (dat) {
//                        dat = eval('(' + _dat + ')');
                        if (dat.status == 0) {
                            layer.alert(dat.msg);
                        } else {
                            jQuery('#table').jqGrid('setCell', rowid, dat.colname, dat.data);
                        }
                    });
                }
            },
            loadComplete: function () {
                $("#gbox_table").css({width:document.getElementById("ibox-content").offsetWidth-30});
            },
            shrinkToFit: true,
            multipleSearch: false,
            multiselect: false,
            width: document.getElementById("ibox-content").offsetWidth-30,
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
            caption: '<a  href="###" onclick="add_notice()"> <i class="fa fa-plus"></i>添加 </a>'
        });
        function search(){
            var province = jQuery("#province").val();
            var city = jQuery("#city").val();
            var url="<?php echo U('index');?>?province="+province+"&city="+city;
            jQuery("#table").jqGrid('setGridParam',{url:url,page:1}).trigger("reloadGrid");
        }
        function set_edit(id){
            layer.open({
                type: 2,
                title: '编辑',
                shadeClose: true,
                shade: 0.8,
                area: ['1020px', '100%'],
                content: "<?php echo U('edit','','');?>/nId/"+id,
                end: function () {
                    $("#table").trigger("reloadGrid");
                }
            });
        }
        function add_notice(){
            layer.open({
                type: 2,
                title: '添加',
                shadeClose: true,
                shade: 0.8,
                area: ['1020px', '100%'],
                content: "<?php echo U('add');?>",
                end: function () {
                    $("#table").trigger("reloadGrid");
                }
            });
        }
        function del_data(id){
            layer.confirm('您确定要删除？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.post("<?php echo U('del');?>",{nId:id},function(data){
                    if(data.status){
                        layer.msg(data.msg, {
                            icon:1,
                            offset: 0,
                            shift: 0,
                            time:1500
                        },function(){
                            $("#table").trigger("reloadGrid");
                        });
                    } else {
                        layer.msg(data.msg, {
                            icon:0,
                            offset: 0,
                            shift: 6,
                            time:1500
                        });

                    }
                })
            }, function(){

            });
        }
        function send(id){
            // $("#send_data").click();
            // $("input[name=nId]").val(id);
            $.post("<?php echo U('send');?>",{'nid':id},function(data){
                if(data.status){
                    layer.msg(data.msg, {
                        icon:1,
                        offset: 0,
                        shift: 0,
                        time:1500
                    },function(){
                        $("#modal-form").click();
                        $("#table").trigger("reloadGrid");
                    });
                } else {
                    layer.msg(data.msg, {
                        icon:0,
                        offset: 0,
                        shift: 6,
                        time:1500
                    });

                }
            })
        }
        $("#submit").click(function(){
            $.post("<?php echo U('send');?>",$("form").serialize(),function(data){
                if(data.status){
                    layer.msg(data.msg, {
                        icon:1,
                        offset: 0,
                        shift: 0,
                        time:1500
                    },function(){
                        $("#modal-form").click();
                        $("#table").trigger("reloadGrid");
                    });
                } else {
                    layer.msg(data.msg, {
                        icon:0,
                        offset: 0,
                        shift: 6,
                        time:1500
                    });

                }
            })
        })
    </script>
    </body>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>