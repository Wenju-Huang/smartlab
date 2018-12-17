<?php
return array(
	//'配置项'=>'配置值'
	
	'TMPL_PARSE_STRING'  =>array(
   		'__PUBLIC__'=>__ROOT__.'/Public/home',
   		'__ORG__'=>__ROOT__.'/Public/org',
	),
	
	// 超级管理员用户id
	'auth_superadmin'=>array(1),


    'DB_FIELDS_CACHE'       =>  false, // 字段缓存信息

	//session
	'VAR_SESSION_ID' 				=> 'SSID',		
);