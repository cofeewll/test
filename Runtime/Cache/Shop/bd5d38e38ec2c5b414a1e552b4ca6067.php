<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title>唯公商城- 商户端登录</title>
    <meta name="keywords" content="唯公商城">
    <meta name="description" content="唯公商城">
    <link href="/Public/Admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="/Public/Admin/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/Public/Admin/css/animate.min.css" rel="stylesheet">
    <link href="/Public/Admin/css/style.min.css" rel="stylesheet">
    <link href="/Public/Admin/css/login.min.css" rel="stylesheet">
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/check.js"></script>
    <script src="/Public/Static/layer/layer.js"></script>
    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->
    <script>
        if(window.top!==window.self){window.top.location=window.location};
    </script>

</head>

<body class="signin" style="background: url('/Public/Admin/img/banner_2.jpg') no-repeat center fixed;background-size: cover">
<div class="signinpanel">
    <div class="row">
        <div class="col-sm-7">
            <div class="signin-info">
                <div class="logopanel m-b">
                    <h1>[唯公商城商户端]</h1>
                </div>
                <div class="m-b"></div>
                <h4>欢迎进入 <strong>唯公商城商户端管理系统</strong></h4>

            </div>
        </div>
        <div class="col-sm-5">
            <form method="post" action="<?php echo U('loginAuth');?>">
                <h4 class="no-margins">登录：</h4>

                <input type="text" class="form-control uname" placeholder="账号" name="username"/>
                <input type="text" class="form-control pword m-b" placeholder="密码" onfocus="this.type='password'" name="password"/>
                <input type="text" class="form-control uname" name="verify" placeholder="验证码" style="width:60%;display: inline;margin-top: 0px;" value="1234"/>
                <a href="javascript:;" onclick="getcode()" class="gettel">&nbsp;&nbsp;获取验证码</a>
                <span class="check-tips"></span>
                <button class="btn btn-success btn-block" type="submit">登录</button>
            </form>
        </div>
    </div>
    <div class="signup-footer">
        <div class="pull-left">
            &copy; 2017 All Rights Reserved.
        </div>
    </div>
</div>
</body>


<!-- Mirrored from www.zi-han.net/theme/hplus/login_v2.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Jan 2016 14:19:52 GMT -->
</html>
<script>
    //表单提交
    $(document)
            .ajaxStart(function(){
                $("button:submit").addClass("log-in").attr("disabled", true);
            })
            .ajaxStop(function(){
                $("button:submit").removeClass("log-in").attr("disabled", false);
            });
    //获取验证码
    var lockl =true;var wait=60;
    function getcode(){
        var phone=$("input[name=username]").val();
        if(phone==''){
            layer.msg("请输入账号");return false;
        }
        $('.gettel').removeAttr("onclick");
        $.post("<?php echo U('getCode');?>",{username:phone,type:5},function(data){
            if(data.status==1){
                //倒计时
                if(!lockl) {
                    return false;
                }else{
                    lockl = false;
                }
                var jishi = setInterval(function() {
                    wait--;
                    $('.gettel').html("重新发送(" + wait + ")");

                    if(wait==0){
                        clearInterval(jishi);
                        $('.gettel').html("获取验证码");
                        $('.gettel').attr("onclick","getcode()")
                        lockl = true;
                        wait=120;
                    }
                },1000);
                layer.open({
                    content: '验证码已发送，注意查收',
                    skin: 'msg',
                    time: 2 //2秒后自动关闭
                });
                return false;
            }else{
                $('.gettel').attr("onclick","getcode()")
                layer.msg("验证码发送失败");return false;
            }
        })
    }


    $("form").submit(function(){
        var self = $(this);
        $.post(self.attr("action"), self.serialize(), success, "json");
        return false;

        function success(data){
            layer.msg(data.msg);
            if(data.status){
                window.location.href = data.data.url;
            }
        }
    });
    $(function(){
        //初始化选中用户名输入框
        $("form").find("input[name=username]").focus();
        //刷新验证码
        var verifyimg = $(".verifyimg").attr("src");
        $(".reloadverify").click(function(){
            if( verifyimg.indexOf('?')>0){
                $(".verifyimg").attr("src", verifyimg+'&random='+Math.random());
            }else{
                $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
            }
        });
    })
    function editPwd(){
        layer.open({
            type: 2,
            title: '修改密码',
            shadeClose: true,
            shade: 0.3,
            area: ['400px', '500px'],
            content: 'editPwd' ,
        });
    }
</script>