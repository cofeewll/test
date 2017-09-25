/**
 * Created by Administrator on 2017/7/26 0026.
 */
function search_on(){
    var loading = layer.load(0, {shade: false});
    $("#order").html("");
    $("#school").html("");
    $("#server").html("");
    $.post("statis",$('form[role="form1"]').serialize(),function(data){
//                console.log(data);
        layer.close(loading)
        var from1=0.00,from2=0.00,from3=0.00;
        $.each(data.data.order,function(a,b){
            if(b.payType==0){
                from1=b.money;
            }
            if(b.payType==1){
                from2=b.money;
            }
            if(b.payType==2){
                from3=b.money;
            }
        })
        var html1="<tr><td>"+from1+"</td><td>"+from2+"</td><td>"+from3+"</td></tr>";
        $("#order").html(html1);
        var html2='';var school = [];var school_value=[];
        $.each(data.data.school,function(a,b){
            html2+="<tr><td>"+b.school_name+"</td><td>"+b.money+"</td></tr>";
            school.push(b.school_name);
            var element = {};
            element.name=b.school_name;
            element.value=b.money;
            school_value.push(element);
        })
        $("#school").html(html2);


        var l=echarts.init(document.getElementById("echarts-pie-chart")),
            u={title:{text:"订单支付方式统计",subtext:"",x:"center"},
                tooltip:{trigger:"item",formatter:"{a} <br/>{b} : {c} ({d}%)"},
                legend:{orient:"vertical",x:"left",data:["余额支付","支付宝","微信"]},
                calculable:!0,
                series:[{name:"支付方式",type:"pie",radius:"55%",center:["50%","60%"],
                    data:[
                        {value:from1,name:"余额支付"},
                        {value:from2,name:"支付宝"},
                        {value:from3,name:"微信"},
                    ]}]};
        l.setOption(u),$(window).resize(l.resize);

        var l=echarts.init(document.getElementById("echarts-pie-chart1")),
            u={title:{text:"商品分类订单统计",subtext:"",x:"center"},
                tooltip:{trigger:"item",formatter:"{a} <br/>{b} : {c} ({d}%)"},
                legend:{orient:"vertical",x:"left",data:school},
                calculable:!0,
                series:[{name:"分类统计",type:"pie",radius:"55%",center:["50%","60%"],
                    data:school_value
                }]};
        l.setOption(u),$(window).resize(l.resize);
        //

    })

}
function search_on1(){
    $("#statis_year").css({display:"none"});
    $("#statis_month").css({display:"none"});
    var loading = layer.load(0, {shade: false});
    $.post("year",$('form[role="form2"]').serialize(),function(data){
        layer.close(loading);
        if($('form[role="form2"]').find(":input[name=year]").val()==1){
            $("#statis_year").css({display:"block"});
            var school = [],school_value=[],school_num=[];
            $.each(data.data.order,function(a,b){
                school.push(b.oYear+"年");
                school_value.push(b.money);
                school_num.push(b.total);
            })


            var t=echarts.init(document.getElementById("echarts-bar-chart")),
                n={
                    title:{text:"按年订单统计"},
                    tooltip:{trigger:"axis"},
                    legend:{data:["订单总量","订单总额"]},
                    grid:{x:30,x2:40,y2:24},
                    calculable:!0,
                    xAxis:[
                        {
                            type:"category",
                            data:school,
                        }
                    ],
                    yAxis:[
                        {
                            type:"value",
                            axisLabel :{
                                // inside:true,
                                margin:-30
                            }
                        }
                    ],
                    series:[
                        {
                            name:"订单总量",
                            type:"bar",
                            data:school_num,
                        },
                        {
                            name:"订单总额",
                            type:"bar",
                            data:school_value,
                        }
                    ]
                };
            t.setOption(n),window.onresize=t.resize;
        }else{
            $("#statis_month").css({display:"block"});
            var school = [],school_value=[],school_num=[];
            $.each(data.data.order,function(a,b){
                school.push(b.oYear+"年"+b.oMonth+"月");
                school_value.push(b.money);
                school_num.push(b.total);
            })


            var t=echarts.init(document.getElementById("echarts-bar-chart1")),
                n={
                    title:{text:"按月订单统计"},
                    tooltip:{trigger:"axis"},
                    legend:{data:["订单总量","订单总额"]},
                    grid:{x:30,x2:40,y2:24},
                    calculable:!0,
                    xAxis:[
                        {
                            type:"category",
                            data:school,
                        }
                    ],
                    yAxis:[{
                        type:"value",
                        axisLabel :{
                            // inside:true,
                            margin:-30
                        }
                    }],
                    series:[
                        {
                            name:"订单总量",
                            type:"bar",
                            data:school_num,
                        },
                        {
                            name:"订单总额",
                            type:"bar",
                            data:school_value,
                        }
                    ]
                };
            t.setOption(n),window.onresize=t.resize;
        }



    })

}
var start1={
    elem:"#start1",
    format:"YYYY-MM",
    max:"2099-06",istime:false,istoday:false,
    choose:function(datas){
        end1.min=datas;
        end1.start=datas
    }};
var end1={
    elem:"#end1",
    format:"YYYY-MM",
    min:laydate.now(),
    max:"2099-06",
    istime:false,istoday:false,
    choose:function(datas){
        start1.max=datas
    }};
laydate(start1);
laydate(end1);