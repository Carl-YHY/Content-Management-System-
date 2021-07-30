<?php
require './init.php';
require COMMON_PATH.'page.php';

//获取GET参数
$cid = I('cid', 'get', 'id');
$page = I('page', 'get', 'page');
$page_size = 5; //每页显示文章数

//定义模板数据
$data = [
	//文档头部信息模块
	'head' => [
		'title' => '首页',
		'keywords' => 'PHP,内容,管理',
		'description' => 'PHP内容管理系统'
	],
	//顶部导航
	'nav' => [
		'curr' => 'index',
		'list' => module_category_nav()
	],
	//首页幻灯片
	'slide' => ($page == 1) ? true : null,
	//文章列表
	'list' => module_article($cid, $page, $page_size),
	//侧边栏
	'sidebar' => [
		'category' => module_category_sidebar(),
		'history' => module_history(),
		'hot' => module_hot()
	]
];

//载入HTML模板
require './view/layout.html'; 