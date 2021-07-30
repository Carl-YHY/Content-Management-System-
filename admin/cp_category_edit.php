<?php
require './init.php';

//获取待修改的栏目ID
$id = I('id', 'get', 'id');

//执行操作
if($_POST){	//有表单提交，处理表单
	F('unset', 'category');  //清理缓存
	doPost($id);
}

//没有表单提交，显示页面
display(null, $id);

//-----------------------------

//处理表单
function doPost($id){
	//处理表单
	$input = [
		'name' => I('name','post','html'),
		'sort' => I('sort','post','int'),
		'pid' =>  I('pid','post','id'),
		'id' => $id
	];
	if($input['pid'] == $id){
		display([false, '修改失败：父节点不能选择自己。'], $id);
	}
	if(!db_fetch(DB_COLUMN, 'SELECT 1 FROM `cms_category` WHERE `id`=? AND `pid`=0', 'i', $input['pid'])){
		display([false, '修改失败：父节点必须是顶级节点。'], $id);
	}
	db_query('UPDATE `cms_category` SET `name`=?,`sort`=?,`pid`=? WHERE `id`=?', 'siii', $input);
	display([true, '修改成功。<a href="cp_category.php">返回列表</a>'], $id);
}
//显示页面
function display($msg=null, $id=0){
	//取出当前栏目数据
	if(!$data = db_fetch(DB_ROW, 'SELECT `pid`,`name`,`sort` FROM `cms_category` WHERE `id`=?', 'i', $id)){
		E('数据不存在！');
	}
	//从数据库取出顶级栏目（不包括自己）
	$category = db_fetch(DB_ALL, 'SELECT `id`,`name` FROM `cms_category` WHERE `pid`=0 AND `id`<>? ORDER BY `sort` ASC', 'i', $id);
	//载入HTML模板
	require './view/category_edit.html';
	exit;
}