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
   <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox ">
                    <div class="ibox-content">
                        <form class="form-inline" id="form-search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="title" id="title" style="width:150px;" placeholder="商家名称">
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control" name="username" id="username" style="width:150px;" placeholder="商家账号">
                            </div>

                            <div class="input-group">
                                <input type="text" class="form-control" name="phone" id="phone" style="width:150px;" placeholder="手机号码">
                            </div>
                            <div class="input-group">
                                <select class="form-control" name="status">
                                    <option value="">审核状态</option>
                                    <option value="-1">待审核</option>
                                    <option value="-2">不通过</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn-sm btn-primary" type="button" id ="search-btn" ><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                            <div class="input-group">
                                <button class="btn-sm btn-primary " type="button" id="search-clear"><i class="fa fa-undo"></i></button>
                                
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
var editUrl = "<?php echo ($editUrl); ?>";
var addUrl = "<?php echo ($addUrl); ?>";
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
            colNames:['商家账号','商家名称','手机号','手续费比例','真实姓名','联系电话','商家地址','商家类别','营业执照','资质证书','审核状态','申请时间','操作','ID'],
            colModel:[
                {name:'username',index:'username',editable: true,sortable: false,align:'center',width:80},
                {name:'title',index:'title',editable: false,sortable: false,align:'center',width:200},
                {name:'phone',index:'phone',editable: false,sortable: false,align:'center',width:100}, 
                {name:'shopFee',index:'shopFee',editable: true,sortable: false,align:'center',width:100}, 
                {name:'realname',index:'realname',editable: false,sortable: false,align:'center',width:100},
                {name:'tel',index:'tel',editable: false,sortable: false,align:'center',width:100}, 
                {name:'address',index:'address',editable: false,sortable: false,align:'center',width:220}, 
                {name:'cate',index:'cate',editable: false,sortable: false,align:'center',width:80},  
                {name:'license',index:'license',editable: false,sortable: false,align:'center',width:80,formatter:function (a,b,c){
                    var val='<img title="营业执照" onclick="showImg(this.src)" src="'+a+'" width="30" height="30">';
                    return val;
                }},  
                {name:'certify',index:'certify',editable: false,sortable: false,align:'center',width:80,formatter:function (a,b,c){
                    var val='<img title="资质证书" onclick="showImg(this.src)" src="'+a+'" width="30" height="30">';
                    return val;
                }},   		
                {name:'status',index:'status',editable: false,sortable: false,align:'center',width:80,formatter:function (a,b,c){
                    if(a == -2){
                        val = '<font style="color:red">拒绝通过</font>';
                    } 
                    else{
                        val = '<font style="color: #ff0aff">待审核</font>';
                    } 
                    return val;
                }},
                {name:'regTime',index:'regTime',editable: false,sortable: true,align:'center',width:120},  
                {width:80,sortable: false,align:'center',formatter: function (a, b, c) {
                        var btn= '<a href="javascript:void(0);" onclick="auditFun(' + c.id + ',1)"><i title="通过申请" class="fa fa-calendar-check-o"></i></a>';
                        if(c.status != -2){
                            btn += '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="auditFun(' + c.id + ',-2)" ><i title="拒绝申请" class="fa fa-calendar-times-o" style="color:red;"></i></a>';
                        }
                        
                        return btn;  
                    }
                },
                {name:'id',index:'id', width:55,align:'center',hidden:'true'},
            ],
            rowNum:10,
            rowList:[10,20,30],
            pager: '#pager',
            sortname: 'regTime',
            viewrecords: true,
            autowidth:true,
            height: 'auto',
            emptyrecords: "暂无数据",
            sortorder: "desc",
            caption:"审核列表&nbsp;&nbsp;<span style='color:orange;font-size: 10px;'>双击行编辑信息</span>",
            onselectrow:true,
            onSelectRow:function(){
                $('#table').jqGrid('restoreRow',lastsel);
            },
            ondblClickRow: function(id){
                $('#table').jqGrid('editRow',id);
                lastsel = id;
                var fee = parseFloat($("#"+id+"_shopFee").val());
                val = (fee > -1)? parseFloat(fee/100).toFixed(2):0;
                $("#"+id+"_shopFee").val(val);
            }, 
        });

        $('#table').delegate('input.editable','keypress', function(e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (code == 13) { //Enter keycode
                var rowData = $('#table').getRowData(lastsel);
                var fee = parseFloat($("#"+lastsel+"_shopFee").val());
                if(!isNumber(fee) || fee<0 || fee>1){
                    layer.msg("请输入0-1之间的小数");
                    return false;
                }
                $('#table').jqGrid('saveRow',lastsel,{
                    url: editUrl,
                    mtype : "POST",
                    extraparam : {id:rowData.id},
                    restoreAfterError: true,
                    successfunc: function(response){
                        layer.msg(response.responseJSON.info);
                        $("#table").trigger("reloadGrid");
                        if( response.responseJSON.status ){
                            return true;
                        } else {
                            return false;
                        }
                    },
                    errorfunc: function(rowid, res){
                        var data = JSON.parse(res.responseText);
                        layer.msg(data.info);
                    }
                });
            }
        }); 

})
    /**
     * [auditFun 审核商家]
     * @param  {[int]} id    [商家id]
     * @param  {[int]} value [审核结果状态]
     * @return {[type]}       [description]
     */
    function auditFun(id,value){
        $.ajax({
            url: "<?php echo U('Shop/dealAudit');?>",
            data: {
                "id": id,
                'value':value,
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
                layer.msg(data.info);
            }
        });
    }

    //显示大图
    function showImg(src){
        var content = '<img src="'+src+'" width="100%"/>';
        //捕获页
        layer.open({
          type: 1,
          shade: false,
          title: false, //不显示标题
          content: content, //捕获的元素
        });
    }       
    </script>



<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>