/**
 * Created by Administrator on 2017/9/12 0012.
 */
$(document).ready(function() {
    $(window).bind("resize",function(){
        var width=$(".jqGrid_wrapper").width();
        $("#table").setGridWidth(width);
    })
});
$.jgrid.defaults.styleUI="Bootstrap";
function search_on(){

    var url="index?"+$("form").serialize();
    jQuery("#table").jqGrid('setGridParam',{url:url,page:1}).trigger("reloadGrid");
}
function un_do(){
    $("form")[0].reset();
    jQuery("#table").jqGrid('setGridParam',{url:"index",page:1}).trigger("reloadGrid");
}
function excel(){
    var url_="excel?"+$("form").serialize();
    location.href=url_;
}
// function tip(v){
//     layer.tips('请选择待发货的订单，然后单击行数据选中，再点击发货输入物流单号就行了', $(v), {
//         tips: [2, '#3595CC'],
//         time: 4000
//     });
// }
function send(){
    var id=$("#table").jqGrid('getGridParam','selrow');
    var rowData = $('#table').getRowData(id);
    if(id==undefined||rowData.order_status!=1){
        layer.msg("请选择待发货的订单");return ;
    }
    $("#show_text").html("订单编号："+rowData.orderSn);
    $("#modal-form").find("table").find("tr").eq(0).find("td").eq(1).html(rowData.amount);
    $("#modal-form").find("table").find("tr").eq(1).find("td").eq(1).html(rowData.realname);
    $("#modal-form").find("table").find("tr").eq(2).find("td").eq(1).html(rowData.address);
    $("#modal-form").find("table").find("tr").eq(3).find("td").eq(1).html(rowData.phone);
    $("#modal-form").find("table").find("tr").eq(4).find("td").eq(1).html(rowData.fee);
    $("form[role=form1]").find("input[name=id]").val(rowData.id);
    $("#ship").click();
}
$("#submit").click(function(){
    var shipName=$(":input[name=shipName]").val();
    var shipId=$("input[name=shipId]").val();
    if(shipId==''||shipName==''){
        layer.msg("请将快递信息填写完整");return false;
    }
    $.post("send",$("form[role=form1]").serialize(),function(data){
        layer.msg(data.msg);
        $("#table").trigger("reloadGrid");
    })
})
function print_(){
    var id=$("#table").jqGrid('getGridParam','selrow');
    var rowData = $('#table').getRowData(id);
    $("form[role=form2]").find("input[name=id]").val(rowData.id);
    $("#show_text1").html("订单编号："+rowData.orderSn+"&nbsp;&nbsp;&nbsp;订单状态："+rowData.status);
    $("#print_2").click();

}
$("#submit1").click(function(){
    $.post("print_",$("form[role=form2]").serialize(),function(data){
        layer.msg(data.msg);
        $("div#print_1").html(data.data);
        $("div#print_1").printArea();
    })
})