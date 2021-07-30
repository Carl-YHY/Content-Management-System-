<?php
require './init.php';

//定义模板数据
$data = [
	//头部信息
	'head' => [
		'title' => '联系我们',
		'keywords' => 'PHP,内容,管理',
		'description' => 'PHP内容管理系统'
	],
	//顶部导航
	'nav' => [
		'curr' => 'about',
		'list' => module_category_nav()
	],
	//文章内容
	'show' => [
		'title' => '联系我们',
		'author' => '传智播客',
		'time' => '2016-04-18 10:27:30',
		'views' => '0',
		'content' => '此页面正在建设中……',
		'cid' => '0',
		'cname' => '无'
	],
	//侧边栏
	'sidebar' => [
		'curr' => '0',
		'category' => module_category_sidebar(),
		'history' => module_history(),
		'hot' => module_hot()
	]
];

//载入HTML模板
require './view/layout.html'; 