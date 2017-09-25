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
    
    <style type="text/css">
        #act_button{
            color:#FFF;background-color:#337ab7;padding:3px 5px;
            font-weight: 400;
            border-radius: 2px;
        }
    </style>

</head>

    <body class="gray-bg">
   <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox ">
                    <div class="ibox-content">
                        <form class="form-inline" id="form-search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="name" id="name" style="width:150px;" placeholder="商品名称">
                            </div>
                            <div class="input-group">
                                <select class="form-control" name="shopId">
                                    <option value="">所属商家</option>
                                    <?php if(is_array($shops)): $i = 0; $__LIST__ = $shops;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                            <div class="input-group">
                                <select class="form-control" name="cid">
                                    <option value="">商品分类</option>
                                    <?php if(is_array($cates)): $i = 0; $__LIST__ = $cates;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                            <div class="input-group">
                                <select class="form-control" name="status">
                                    <option value="">状态</option>
                                    <option value="1">已通过</option>
                                    <option value="-1">待审核</option>
                                    <option value="-2">拒绝通过</option>
                                    <option value="-3">强制下架</option>
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
var sortUrl = "<?php echo ($sortUrl); ?>";
var addUrl = "<?php echo ($addUrl); ?>";
var optUrl = "<?php echo ($optUrl); ?>";
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
            colNames:['商品ID','商品名称','货号','商家名称','分类','价格','库存','新品','热销','审核状态','排序','操作'],
            colModel:[
                {name:'id',index:'id', width:100,align:'center',formatter:function (a,b,c){
                    var str ='<input type="checkbox" name="goods_id[]" value="'+a+'" />';
                    return str+'&nbsp;'+a;
                }},
                {name:'name',index:'name',editable: false,sortable: false,align:'center',width:400},
                {name:'goodsSn',index:'goodsSn',editable: false,sortable: false,align:'center',width:100},
                {name:'title',index:'title',editable: false,sortable: false,align:'center'},
                {name:'cid',index:'cid',editable: false,sortable: false,align:'center'},
                {name:'price',index:'price',editable: false,sortable: false,align:'center',width:100},
                {name:'stock',index:'stock',editable: false,sortable: false,align:'center',width:100},
                {name:'isNew',index:'isNew',editable: false,sortable: false,align:'center',width:80,formatter:function (a,b,c){
                    if(a == 0){
                        val = '<i class="fa fa-close" style="color:red"></i>';
                    } 
                    else{
                        val = '<i class="fa fa-check" style="color:#00a157"></i>';
                    } 
                    return '<a href="javascript:void(0);" onclick="changeNew('+c.id+','+a+')">'+val +'</a>';
                }},
                {name:'isHot',index:'isHot',editable: false,sortable: false,align:'center',width:80,formatter:function (a,b,c){
                    if(a == 0){
                        val = '<i class="fa fa-close" style="color:red"></i>';
                    } 
                    else{
                        val = '<i class="fa fa-check" style="color:#00a157"></i>';
                    } 
                    return '<a href="javascript:void(0);" onclick="changeHot('+c.id+','+a+')">'+val +'</a>';
                }},
                {name:'status',index:'status',editable: false,sortable: false,align:'center',width:100,formatter:function (a,b,c){
                    if(a == -1){
                        return '<font style="color: #ff0aff">待审核</font>';
                    }else if(a == -2){
                        return '<font style="color: red;">拒绝通过</font>';
                    }else if(a == -3){
                        return '<font style="color: red;">强制下架</font>';
                    }else{
                        return '<font style="color: #00a157;">已通过</font>';
                    }
                    
                }},
                {name:'sort',index:'sort',editable: false,sortable: false,align:'center',width:100,formatter: function (a, b, c) {
                        var sort = '';
                        sort = "<input type='text' style='width:80px;' onchange='updateSort("+c.id+",this)' onkeydown='clearFun()' class='form-control input-sm ' value='" + a + "'>";
                        return sort; 
                    }
                },
                {width:100,sortable: false,align:'center',formatter: function (a, b, c) {
                    var btn = '';
                        if(c.status != -3){
                            btn += '&nbsp;&nbsp;<a href="javascript:void(0);" goods_id="'+c.id+'" goods_name="'+c.name+'" goods_sn="'+c.goodsSn+'" onclick="takeoff(this)"><i title="强制下架" class="fa fa-ban"></i></a>';
                        }
                        
                        btn += '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="addFun(' + c.id + ')" ><i title="查看详情" class="fa fa-search"></i></a>';
                        
                        
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
            sortorder: "desc",
            caption:"&nbsp;全选&nbsp;<input type='checkbox' id='checkAll'/>&nbsp;<select name='opt' id='opt'><option value=''>请选择</option><option value='new'>新品</option><option value='hot'>热销</option><option value='access' selected>审核通过</option><option value='refuse'>拒绝通过</option></select>&nbsp;<a id='act_button' href='javascript:;' onclick='act_submit();'>确定</a>&nbsp;<a href='javascript:freshFun();'><i class='fa fa-refresh'></i></a>",
            onselectrow:false,
            onSelectRow:function(){
                // $('#table').jqGrid('restoreRow',lastsel);
            },
            ondblClickRow: function(id){
                // $('#table').jqGrid('editRow',id);
                // lastsel = id;
            },
        });
})

        
    </script>
    <script type="text/javascript">
        $(function(){
            $('#checkAll').click(function(){
                $("input[name='goods_id[]']").prop('checked', this.checked);
            });
        });

        function act_submit(){
            var goods_id =[]; 
            $('input[name="goods_id[]"]:checked').each(function(){ 
                goods_id.push($(this).val()); 
            });
            var opt = $('#opt').val();
            // console.log(goods_id);
            if(goods_id.length == 0){
                layer.msg('请勾选要操作的商品');
                return false;
            }
            if(opt ==''){
                layer.msg('请选择操作');
                return false;
            }
            if(opt == 'refuse'){
                var html='<div class="col-sm-12"><div class="form-group"><label>请输入操作备注：</label><input type="text" id="remark" class="form-control" name="remark" value=""></div></div>';
                layer.confirm(html, {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    var remark = $.trim($('#remark').val());
                    if( remark == '' ){
                        layer.msg('请填写操作备注');
                        return false;
                    }
                    request_net(goods_id ,opt, remark);
                }, function(){

                });
                // layer.prompt({title: '请输入操作备注(<b style="color:red;">必填</b>)', formType: 2}, function(text, index){
                //     layer.close(index);
                //     request_net(goods_id, text);
                // });
            }else{
                request_net(goods_id,opt, '无备注');
            }
            // request_net(goods_id,opt, '无备注');
        }
        function changeNew(id,value){
            var newUrl = "<?php echo ($newUrl); ?>";
            $.ajax({
                url: newUrl,
                data: {"id": id,"value":value,type:'new'},
                type: "post",
                success: function (data) {
                    if (data.status) {
                        $("#table").trigger("reloadGrid");
                    } else {
                        layer.msg(data.info, {
                            icon:0,
                            offset: 0,
                            shift: 6,
                            time:1500
                        });
                    }
                }, 
                error: function (error) {
                    alert(data.info);
                }
            });
        }
        function changeHot(id,value){
            var newUrl = "<?php echo ($newUrl); ?>";
            $.ajax({
                url: newUrl,
                data: {"id": id,"value":value,type:'hot'},
                type: "post",
                success: function (data) {
                    if (data.status) {
                        $("#table").trigger("reloadGrid");
                    } else {
                        layer.msg(data.info, {
                            icon:0,
                            offset: 0,
                            shift: 6,
                            time:1500
                        });
                    }
                }, 
                error: function (error) {
                    alert(data.info);
                }
            });
        }
    </script>
    <script>
    //批量修改商品状态
    function request_net(goods_id ,opt, text){
        
        $.ajax({
            url: optUrl,
            data: {"goods_id": goods_id,"opt":opt,text:'text'},
            type: "post",
            success: function (data) {
                if (data.status) {
                    layer.msg(data.info);
                    $("#table").trigger("reloadGrid");
                } else {
                    layer.msg(data.info, {
                        icon:0,
                        offset: 0,
                        shift: 6,
                        time:1500
                    });
                }
            }, 
            error: function (error) {
                layer.msg(data.info);
            }
        });
    }
    //下架违规商品
    function takeoff(obj){
        var reasonhtml = '<div style="position: relative;">';
        reasonhtml += '<div style="margin: 0px; padding: 0px;">';
        reasonhtml += '<div style="margin:20px;">';
        reasonhtml += '<div style="margin:5px 10px;">';
        reasonhtml += '<label>违规商品货号</label><span style="margin-left:10px;">'+$(obj).attr('goods_sn')+'</span></div>';
        reasonhtml += '<div style="margin:5px 10px;">';
        reasonhtml += '<label>违规商品名称</label><span style="margin-left:10px;">'+$(obj).attr('goods_name')+'</span></div>';
        reasonhtml += '<div style="margin:5px 10px;">';
        reasonhtml += '<label for="close_reason">违规下架理由</label>';
        reasonhtml += '<span><input type="hidden" id="take_goods_id" value="'+$(obj).attr('goods_id')+'"></span>';
        reasonhtml += '<div>';
        reasonhtml += ' <textarea rows="6" class="tarea" cols="60" name="close_reason" id="close_reason"></textarea>';
        reasonhtml += ' </div></div>';
        reasonhtml += '<div style="text-align:center;"><a href="javascript:void(0);" onclick="takeoff_goods();" class="btn btn-primary" nctype="btn_submit">确认提交</a></div>';
        reasonhtml += '</div></div></div>'
        layer.open({
              type: 1,
              title:'违规下架理由',
              skin: 'layui-layer-rim', //加上边框
              area: ['620px', '340px'], //宽高
              content: reasonhtml
        });
    }
    
    function takeoff_goods(){
          $.ajax({
              type: "POST",
              url: optUrl,//+tab,
              data: {opt:'takeoff',goods_id:$('#take_goods_id').val(),text:$('#close_reason').val()},
              dataType: 'json',
              success: function (data) {
                  if(data.status == 1){
                      layer.msg(data.info);
                      $("#table").trigger("reloadGrid");
                      $('.layui-layer-close1').click();
                  }else{
                      layer.msg(data.info);
                  }
              },
              error:function(){
                  layer.msg('网络异常');
              }
          });   
    }
</script>


<script src="/Public/Admin/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/func.js"></script>
</html>