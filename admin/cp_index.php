<?php
require './init.php';
//获取MySQL版本信息
function getMySQLVer(){
	$data = db_fetch(DB_ROW, 'SELECT VERSION() AS ver');
	return isset($data['ver']) ? $data['ver'] : '未知';
}
//获取服务器基本信息
$data = [
	//获取服务器信息（操作系统、Apache版本、PHP版本）
	'server' => I('SERVER_SOFTWARE', 'server', 'html'),
	//获取MySQL版本信息
	'mysql' => getMySQLVer(),
	//获取服务器时间
	'time' => date('Y-m-d H:i:s', time()),
	//上传文件大小限制
	'max_upload' => ini_get('file_uploads') ? ini_get('upload_max_filesize') : '已禁用', 
	//脚本最大执行时间
	'max_ex_time' => ini_get('max_execution_time').'秒', 
];
//载入页面
require './view/index.html';