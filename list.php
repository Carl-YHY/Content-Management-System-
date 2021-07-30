<?php
require './init.php';
require COMMON_PATH.'page.php';

//获取GET参数
$cid = I('cid', 'get', 'id');
$page = I('page','get','page');
$page_size = 5; //每页显示文章数

//定义模板数据
$data = [
	//顶部导航
	'nav' => [
		'curr' => 'cid_'.module_category_top($cid),
		'list' => module_category_nav()
	],
	//文章列表
	'list' => module_article($cid, $page, $page_size),
	//侧边栏
	'sidebar' => [
		'category' => module_category_sidebar($cid),
		'history' => module_history(),
		'hot' => module_hot()
	]
];
$data['head'] = [
	'title' => $data['list']['title'].' 栏目',
	'keywords' => $data['list']['title'].',PHP,内容,管理',
	'description' => 'PHP内容管理系统'
];

//载入HTML模板
require './view/layout.html'; 