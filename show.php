<?php
require './init.php';

$id = I('id', 'get', 'id');


db_query('UPDATE `cms_article` SET `views`=`views`+1 WHERE `id`=? AND `show`=\'yes\' AND `views`<4294967295', 'i', $id);


$data = [
	'head' => [
		'title' => '查看文章',
		'keywords' => 'PHP,内容,管理',
		'description' => 'PHP内容管理系统'
	],

	'nav' => [
		'curr' => 'show',
		'list' => module_category_nav()
	],

	'show' => module_article_show($id),

	'sidebar' => [
		'category' => module_category(),
		'history' => module_history($id),
		'hot' => module_hot()
	]
];

if($data['show']){

	$data['show']['cname'] = module_category_name($data['show']['cid']);
	$data['nav']['curr'] = 'cid_'.module_category_top($data['show']['cid']);
	
	$data['show']['change'] = [
		'prev' => db_fetch(DB_ROW, "SELECT `id`,`title` FROM `cms_article` WHERE `id`<? AND `show`='yes' ORDER BY `id` DESC LIMIT 1", 'i', $id),
		'next' => db_fetch(DB_ROW, "SELECT `id`,`title` FROM `cms_article` WHERE `id`>? AND `show`='yes' LIMIT 1", 'i', $id)
	];
	
	$data['head'] = [
		'title' => $data['show']['title'],
		'keywords' => $data['show']['keywords'],
		'description' => $data['show']['description']
	];
	$data['sidebar']['category'] = module_category_sidebar($data['show']['cid']);
}else{
	$data['show'] = [];
	$data['sidebar']['category'] = module_category_sidebar();
}


require './view/layout.html';