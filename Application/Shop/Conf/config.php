<?php
return array(
	// 模板常量设置
    'TMPL_PARSE_STRING'     =>array(
        '__STATIC__' =>__ROOT__.'/Public/Static',
        '__ADMIN__' =>__ROOT__.'/Public/Admin',
    ),
    'SESSION_PREFIX' => 'shop_admin', //session前缀
    'COOKIE_PREFIX'  => 'shop_admin_', // Cookie前缀 避免冲突
);