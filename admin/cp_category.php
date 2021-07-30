<?php
require './init.php';

//接收参数
$action = I('a', 'get', 'string');

//删除操作
if($action == 'del'){	
	$del_id = I('id', 'get', 'id');
	//先判断是否有子级
	if(db_fetch(DB_COLUMN, 'SELECT 1 FROM  `cms_category` WHERE `pid`=?', 'i', $del_id)){
		display([false, '该栏目下有子级栏目，不能删除。']);
	}else{
		F('unset', 'category');  //清理缓存
		//删除栏目
		db_query('DELETE FROM `cms_category` WHERE `id`=?', 'i', $del_id);
		//将栏目下的文章的所属栏目设置为0
		db_query('UPDATE `cms_article` SET `cid`=? WHERE `cid`=?', 'ii', [0, $del_id]);
		display([true, '删除成功。']);
	}
}

//处理表单
elseif($_POST){
	F('unset', 'category');  //清理缓存
	saveData();  //修改
	addData();   //添加
	display([true, '保存成功。']);
}

//显示页面
display();

//--------------------------------

//修改数据
function saveData(){
	$result = [];
	foreach(I('save','post','array') as $k=>$v){
		$result[] = [
			'name' => I('name', $v, 'html'),
			'sort' => I('sort', $v, 'int'),
			'id' => I(null, null, 'id', $k)
		];
	}
	if(!empty($result)){
		db_query('UPDATE `cms_category` SET `name`=?, `sort`=? WHERE `id`=?', 'ssi', $result);
	}
}
//添加数据
function addData(){
	$result = [];
	foreach(I('add','post','array') as $v){
		$result[] = [
			'pid' => I('pid', $v, 'id'),
			'name' => I('name', $v, 'html'),
			'sort' => I('sort', $v, 'int')
		];
	}
	if(!empty($result)){
		db_query('INSERT INTO `cms_category` (`pid`,`name`,`sort`) VALUES (?,?,?)', 'isi', $result);
	}
}
//显示页面
function display($msg=null){
	//从数据库取出数据
	$data = module_category('pid');
	//载入HTML模板
	require './view/category.html';
	exit;
}