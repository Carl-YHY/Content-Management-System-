<?php
require './init.php';

//获取操作
$action = I('a','get','string');

//删除操作
if($action=='del'){
	//获取待删除的记录ID
	$del_id = I('id','get','id');
	//删除记录前删除原来的图片
	if($del_thumb = db_fetch(DB_COLUMN, 'SELECT `thumb` FROM `cms_article` WHERE `id`=?', 'i', $del_id)){
		del_file(UPLOAD_PATH.$del_thumb);	//删除文件
	}
	//删除记录
	db_query('DELETE FROM `cms_article` WHERE `id`=?', 'i', $del_id);
	//显示信息
	display([true, '删除记录成功。']);
}

//显示页面
display();

//-----------------------------------------------

function display($msg=null, $page_size=5){
	//获取列表参数
	$cid = I('cid','get','id');				//栏目ID
	$page = I('page','get','page'); 		//页码（限制最小值为1）
	$search = I('search','get','html');		//搜索关键字
	$order = I('order','get','string');		//排序条件
	//拼接ORDER条件
	$order_arr = [
		'time-asc' => ['name'=>'时间降序', 'sql'=>'a.`id` DESC'],
		'time-desc' => ['name'=>'时间升序', 'sql'=>'a.`id` ASC'],
		'show-desc' => ['name'=>'发布状态', 'sql'=>'a.`show` DESC']
	];
	$sql_order = ' ORDER BY ';
	$sql_order .= isset($order_arr[$order]) ? $order_arr[$order]['sql'] : 'a.`id` DESC';
	//拼接WHERE条件
	$sql_where = ' WHERE 1=1 ';
	$sql_where .= $cid ? ' AND a.`cid` IN ('.module_category_sub($cid).')' : '';
	$sql_where .= ' AND a.`title` LIKE ? ';
	$sql_search = '%'.db_escape_like($search).'%';
	//拼接LIMIT条件
	require COMMON_PATH.'page.php';//载入分页函数
	$sql_limit = ' LIMIT '.page_sql($page, $page_size);
	//获取记录总数
	$page_total = db_fetch(DB_COLUMN, 'SELECT COUNT(*) FROM `cms_article` AS a '.$sql_where, 's', $sql_search);
	//查询列表
	$data = db_fetch(DB_ALL, 'SELECT a.`id`,a.`cid`,a.`title`,a.`author`,a.`show`,a.`time`,c.`name` AS cname'
			.' FROM `cms_article` AS a LEFT JOIN `cms_category` AS c ON a.`cid`=c.`id`'
			."$sql_where $sql_order $sql_limit", 's', $sql_search);
	//获取栏目数据
	$category = module_category('pid');
	//获取分页HTML
	$page_html = page_html($page_total, $page, $page_size, 8);
	//判断页码和结果
	if(!$data){
		if($page > 1){
			redirect('?page=1'); //不是第1页时，跳回第一页
		}else{
			$msg = [true, '没有查找到记录。'];
		}
	}
	require './view/article.html';
	exit;
}