
//表单提交
$(document)
	.ajaxStart(function(){
		$("button:submit").addClass("btn-default").removeClass('btn-primary').attr("disabled", true);
	})
	.ajaxStop(function(){
		$("button:submit").removeClass("btn-default").addClass("btn-primary").attr("disabled", false);
	});
//单选框，多选框
$(document).ready(function(){
    $(".i-checks").iCheck({
        checkboxClass:"icheckbox_square-green",
        radioClass:"iradio_square-green",
    })
});

//搜索
$('#search-btn').on('click', function() {
    var self = $("#form-search");
    $("#table").jqGrid('setGridParam', { 
        url: dataUrl, 
        postData: self.serialize(), 
        page: 1 
    }).trigger("reloadGrid"); 
    
});
//清除搜索
$('#search-clear').on('click',function () {
	$("form")[0].reset();
    $("#table").jqGrid('setGridParam',{url:dataUrl,postData:'',page:1}).trigger("reloadGrid");
});

//添加表单提交
$("#form-add").submit(function(){
	
	var self = $(this);
	if($(document).find('#editor').length > 0){
		editor.sync(); 
	}
	$.post(self.attr("action"), self.serialize(), success, "json");
	
	return false;

	function success(data){
		if(data.status){
			layer.msg(data.info, {
				icon:1,
				offset: 0,
				shift: 0,
				time:1500
			},function(){
				window.location.reload();//刷新当前页面 ;
			});
		} else {
			layer.msg(data.info, {
				icon:0,
				offset: 0,
				shift: 6,
				time:1500
			}); 
			 
		}
	}
});

//编辑表单
$("#form-edit").submit(function(){
	var self = $(this);
	if($(document).find('#editor').length > 0){
		editor.sync(); 
	}
	$.post(self.attr("action"), self.serialize(), success, "json");
	
	return false;
	function success(data){
		if(data.status){
			layer.msg(data.info, {
				icon:1,
				offset: 0,
				shift: 0,
				time:1500
			},function(){
				window.location.reload();//刷新当前页面 ;
			});
		} else {
			layer.msg(data.info, {
				icon:0,
				offset: 0,
				shift: 6,
				time:1500
			}); 
			 
		}
	}
});

//刷新
function freshFun(){
	if(dataUrl){
		window.location.href=dataUrl;
	}
}
//添加、编辑
function addFun(id) {
    var title;
    if(addUrl){
    	if(id>0){
	        title = '编辑记录';
	        url = addUrl+'/id/' + id ;
	    }else{
	        title = '添加记录';
	        url = addUrl;
	    }
	    layer.open({
	        type: 2,
	        title: title,
	        shadeClose: true,
	        shade: 0.3,
	        area: ['80%', '95%'],
	        content: url, 
	        end: function () {
	            $("#table").trigger("reloadGrid");
	        }
	    });
    }
}

//删除操作
function deleteFun(id) {
    layer.confirm('确定删除该记录ID=' + id + '吗？', {
        btn: ['确认', '取消'], //按钮
        shade: false //不显示遮罩
    }, function() {
        _deleteFun(id);
    }, function() {
    });
}

function _deleteFun(id) {
	if(delUrl){
		$.ajax({
	        url: delUrl,
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
}

function clearFun(){
    if(!(event.keyCode==46)&&!(event.keyCode==8)&&!(event.keyCode==37)&&!(event.keyCode==39))
    if(!((event.keyCode>=48&&event.keyCode<=57)||(event.keyCode>=96&&event.keyCode<=105))) 
    event.returnValue=false; 
}
//修改排序
function updateSort(id,obj){
    var value = $(obj).val();
    if(sortUrl){
    	$.ajax({
	        url: sortUrl,
	        data: {"id": id,"value":value},
	        type: "post",
	        success: function (data) {
	            if (data.status) {
	                layer.msg(data.info, {
	                    icon:1,
	                    offset: 0,
	                    shift: 0,
	                    time:1500
	                },function(){
	                    $("#table").trigger("reloadGrid");
	                });
	                
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
}
//修改状态
 function changeStatus(id,value){
 	if(showUrl){
 		$.ajax({
	        url: showUrl,
	        data: {
	            "id": id,
	            'value':value,
	        },
	        type: "post",
	        success: function(data) {
	            if (data.status) {
	            	$("#table").trigger("reloadGrid");
	                // layer.msg(data.info, {
	                //     icon: 1,
	                //     offset: 0,
	                //     shift: 0,
	                //     time: 1500
	                // }, function() {
	                    
	                // });

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
}