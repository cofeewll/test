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
                    <form class="form-inline" id="form-search">
                        <div class="input-group">
                            <input type="text" class="form-control" name="username" id="username" style="width:150px;" placeholder="昵称">
                        </div>

                        <div class="input-group">
                            <input type="text" class="form-control" name="mobile" id="mobile" style="width:150px;" placeholder="手机号码">
                        </div>

                            <div class="input-group">
                            <select class="form-control" name="type" id="type"  style="width:150px;" >
                                <option value="" >请选择角色</option>
                                <?php if(is_array($type)): foreach($type as $key=>$vo): ?><option value="<?php echo ($vo['id']); ?>" ><?php echo ($vo['title']); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>

                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn-sm btn-primary" type="button" id ="search-btn" ><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                        <div class="input-group">
                            <button class="btn-sm btn-primary" type="button" id="search-clear"><i class="fa fa-undo"></i></button>
                        </div>
                    </form>
                </div>
                    <div class="ibox-content">
                        <div class="jqGrid_wrapper">
                            <table id="table" style="border-collapse: collapse"></table>
                            <div id="pager"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
<script>
var dataUrl = "<?php echo ($dataUrl); ?>";
var addUrl = "<?php echo ($addUrl); ?>";
var delUrl = "<?php echo ($delUrl); ?>";
var showUrl = "<?php echo ($showUrl); ?>";
$(function(){
    $(document).ready(function() {
        $(window).bind("resize",function(){
            var width=$(".jqGrid_wrapper").width();
            $("#table").setGridWidth(width);
        })
    });
    $.jgrid.defaults.styleUI="Bootstrap";
    var lastsel;
        $("#table").jqGrid({
            url:dataUrl,
            datatype: "json",
            colNames:['ID','昵称','角色','手机号码','最后登录时间','最后登录IP','状态','操作'],
            colModel:[
                {name:'id',index:'id', width:55,align:"center"},
                {name:'username',index:'username',editable: true,sortable: false,align:"center"},
                {name:'title',index:'title',editable: true,sortable: false,align:"center"},
                {name:'mobile',index:'mobile',editable: true,sortable: false,align:"center"},
                {name:'loginTime',index:'loginTime',editable: true,sortable: false,align:"center"},
                {name:'loginIp',index:'loginIp',editable: true,sortable: false,align:"center"},
                {name:'status',align:"center",index:'status',formatter:function (a,b,c){
                    if(a == 0){
                        return "<span style='color:red'>禁用</span>";
                    }else if(a == -1){
                        return "<span style='color:orangered'>锁定</span>";
                    }
                    else{
                        return "<span style='color:green'>正常</span>";
                    }
                }},
                {width:100,sortable: false,align:"center",formatter: function (a, b, c) {
                        var btn = '<a href="javascript:void(0);" onclick="addFun(' + c.id + ')"  ><i class="fa fa-edit"></i></a>';
                        if(c.status == -1){
                            btn += '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="unLock(' + c.id + ')"><i class="fa fa-key"></i></a>';
                        }
                        if( c.id != 1 ) {
                            btn += '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteFun(' + c.id + ')" ><i class="fa fa-trash"></i></a>';
                        }
                        return btn; 
                    }
                },
            ],
            rowNum:10,
            rowList:[10,20,30],
            pager: '#pager',
            sortname: 'id',
            viewrecords: true,
            autowidth:true,
            height: 'auto',
            emptyrecords: "暂无数据",
            sortorder: "asc",
            caption:"<a href='javascript:void(0)' onclick='addFun()'><i class='fa fa-plus'></i>添加</a>",
            onselectrow:true,
            onSelectRow:function(){
                //$('#table').jqGrid('restoreRow',lastsel);
            },
            ondblClickRow: function(id){
                //$('#table').jqGrid('editRow',id);
                //lastsel = id;
            }, 
        });
});
    function unLock(id){
        $.ajax({
            url: "<?php echo U('unLock');?>",
            data: {
                "id": id
            },
            type: "post",
            success: function(data) {
                if (data.status) {
                    layer.msg(data.info, {
                        icon: 1,
                        offset: 0,
                        shift: 0,
                        time: 1500
                    }, function() {
                        $("#table").trigger("reloadGrid");
                    });
                } else {
                    layer.msg(data.info, {
                        icon: 0,
                        offset: 0,
                        shift: 6,
                        time: 1500
                    });
                }
            },
            error: function(error) {
                alert(data.info);
            }
        });
    }
        
    </script>



<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>