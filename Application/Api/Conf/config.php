<?php
return array(
	//'配置项'=>'配置值'
	//'TMPL_EXCEPTION_FILE' => APP_PATH.'/../Public/exception.tpl'

	'alipayConfig' => array(
                'appid' =>'2017051907283383',//商户密钥
                'rsaPrivateKey' =>'MIIEpQIBAAKCAQEAu0CthJCXgrMc7S1iV+TLkoC5r+usin+rbjK5iiM42ut4+1NNrTFcSnZugezMYtsnKW79PDvsn600fvkmKq/9OhIpdk2rZmnZbJkzDz7BFUngQtvkam9YY5yonw/JIh5COUlPpoL979cEpfRKEunz+/NaPr+XVO42fSuZ/AAFISgUV+ve5OU/1r4ofbGW/fGa48ofFkqC2vy9HYmOG21el8gpYICc10+wuMoDFao1pYMpne7G6afA50HD0bsQnVuyRiNZPArhrhekyxL3EoXmrj7cPaCkHmBGLeCWfvIblX213Cyhg6PoA9tHTGNMmh01D4Ot0VlHBeL5PQSIZaiZGQIDAQABAoIBAFUdAAIaqxOYkJRqJaJn9/RemIiTKjlF8MlFOzrD6crwb2xloBASOK4MCQz7cqeYj8NwlKC1aEfoNc1lOGj817B2ouwIP9lsUYzgUeojDya7Drm1M60BcX7NrYOnbDga4uUhIphAQGatKIq6cJUKTIGASsvs0D+li5T3paV66Nn5kz+iMFq1BlIUiG2Y3WGZolBjCZVrOWHgJ+N99pkGwgkPEuYkHOjwANI0Wcn0clM82XoopK2eGecjuNYUpK/JEHSWurpGOjRKq2nbMva08BbBIhFSyOZGLX6wAM8k7ZaIzgWjjD1U6I75v1F+1YExJtaLSTG+O/KS0+nC/wZhbZECgYEA60U/2XzmCq1XsJOA5QIPwLZZF8pYUfs2lSbdczhBiBypS71epaTrtj46mRuJGwRIBWHSjcKurLD5TP7c3m2pvwlHFw0ZwiAjrGuCnwXZOhUdtVu9NX6nMbZOpbANAJxoztx2Ez3rpDY+KZIIxGQttJAFMYSpM88iOwaE9+eOsD0CgYEAy8BW/pEFNLSRswfAEvGJArJtW/Voi83TW7FJk5zjZn0JOhzuonHQOBgmwk9Tc9plM7Hnc0ULY0gD5zkwnvZv2nTN5UapPaU8EODTf0xP8MZ1svCX99/mj2kpojvoxMPL/x4n7QdCSlpGJzagfncSG9vifCc5TZoFg8vBewN0ng0CgYEAsn0/C4kuHTuVI2lEiHkf6lxtCxeAGfI0XsO1jgxH6zOTXNq2S4TrLr/XRQEW62A8I5krXFEuo+U/vaI9fqJD/aLURDC0MgqgBdcqqeqN+vq6JzUKPuNvhM+Cq0D9HFGNUiL/kWmQtGxSjMc1z4kRA8OfrQmwNJYYqh8wEcOjBSECgYEAjXKssjOJMqq8j1YtELmfjUDDTNjUyGGOfpRYaOK8t3TMSAy32gw7KgYSOkkJvV77bOTvsgVXv4fKNeZtC543zqfSHH5ulX41cd4y1afrw1JzeVMvnQRSMnMmVRtUH+1xnF2aCCJG8gkztJtm1gThjHedBuXQEUCA3ZtBaZ3fgq0CgYEAyiiSV+YQR0rO7xOHJ2Yde0gg8UnhjlOLjpnxz1Tj78JkwdPurjmvPE6COkZdz3rGmRN9GJalGbGFw5BxmRTUAMTXa+P87APf0umncRIsj+dBWvPDUMJQbZ/8BKnd2ycupjHMpDBZVgvD+h57oN6U9OG3MSd2GGxXcMM4b30tOmI=',//私钥
    //            'alipayrsaPublicKey'=>'F:/website/zyorder/Api/alipayapp/RSA/1public2048.txt',//公钥(自己的程序里面用不到)
                'partner'=>'2088421540577515',//(商家的参数,新版本的好像用不到)
                'input_charset'=>strtolower('utf-8'),//编码
                'notify_url' =>'http://'.$_SERVER['HTTP_HOST']. '/index.php/Api/Notify/notifyHandleAli',//回调地址(支付宝支付成功后回调修改订单状态的地址)
                'payment_type' =>1,//(固定值)
                'seller_id' =>'',//收款商家账号
                'charset'    => 'utf-8',//编码
                'sign_type' => 'RSA2',//签名方式
                'timestamp' =>date("Y-m-d H:i:s"),
                'version'   =>"1.0",//固定值
                'url'       => 'https://openapi.alipay.com/gateway.do',//固定值
                'method'    => 'alipay.trade.app.pay',//固定值
    ),
);