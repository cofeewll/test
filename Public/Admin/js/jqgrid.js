/**
 * Created by Administrator on 2017/8/21 0021.
 */
$(document).ready(function() {
    $(window).bind("resize",function(){
        var width=$(".jqGrid_wrapper").width();
        $("#table").setGridWidth(width);
    })
});
$.jgrid.defaults.styleUI="Bootstrap";
function getGrid(obj,loadComplete,ondblClickRow,onCellSelect,sortname,caption,reurl,footerrow){
    $('#table').jqGrid({
        url: reurl,
        datatype: "json",
        colModel:obj,
        loadComplete:loadComplete ,
        ondblClickRow:ondblClickRow ,
        onCellSelect:onCellSelect,
        footerrow: footerrow,
        shrinkToFit: true,
        multipleSearch: false,
        multiselect: false,
        autowidth:true,
        gridview: true,
        mtype: 'post',
        height: 'auto',
        rowNum: 10,
        rowList: [10, 20, 50, 100],
        pager: '#pager',
        emptyrecords: "暂无数据",
        sortname: sortname,
        viewrecords: true,
        sortorder: "desc",
        rownumbers:true,
        caption: caption
    });
}
function search(url){
    var url=url+"?"+$('form[role="form"]').serialize();
    jQuery("#table").jqGrid('setGridParam',{url:url,page:1}).trigger("reloadGrid");
}
function un_do(url){
    $("form.form-inline")[0].reset();
    jQuery("#table").jqGrid('setGridParam',{url:url,page:1}).trigger("reloadGrid");
}
