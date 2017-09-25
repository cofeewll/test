/**
 * Created by Administrator on 2017/7/7 0007.
 */
var start={
    elem:"#start",
    format:"YYYY/MM/DD",
    max:"2099-06-16",istime:false,istoday:false,
    choose:function(datas){
        end.min=datas;
        end.start=datas
    }};
var end={
    elem:"#end",
    format:"YYYY/MM/DD",
    min:laydate.now(),
    max:"2099-06-16",
    istime:false,istoday:false,
    choose:function(datas){
        start.max=datas
    }};
laydate(start);
laydate(end);

var ajax_call_back=function(data){
    if(data.status){
        layer.msg(data.msg, {
            icon:1,
            offset: 0,
            shift: 0,
            time:1500
        },function(){
            window.location.reload();//刷新当前页面 ;
        });
    } else {
        layer.msg(data.msg, {
            icon:0,
            offset: 0,
            shift: 6,
            time:1500
        });

    }
}

