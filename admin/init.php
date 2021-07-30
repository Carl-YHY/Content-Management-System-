<?php
//设置字符集
header('content-type:text/html;charset=utf-8');
//调试开关
define('APP_DEBUG', true);
//开启调试时，显示错误报告
ini_set('display_errors', APP_DEBUG);
//定义前后台公共目录常量
define('COMMON_PATH','../common/');   //公共文件目录
define('UPLOAD_PATH', '../upload/');  //上传文件目录
//载入函数库
require COMMON_PATH.'function.php';
//启动session
session_start();
//为项目创建Session，统一保存到cms中
if(!isset($_SESSION['cms'])){
	$_SESSION = ['cms' => []];
}
//检查用户登录
if(!defined('NO_CHECK_LOGIN')){
	if(isset($_SESSION['cms']['admin'])){
		$user = $_SESSION['cms']['admin'];  //用户已登录，取出用户信息
	}else{
		redirect('login.php');  //用户未登录，跳转到登录页面
	}
}
//载入数据库函数
require COMMON_PATH.'db.php';
//载入模块
require COMMON_PATH.'module.php';
//生成CSRF令牌
define('TOKEN', token_get());
//自动验证CSRF令牌
if(($_POST || isset($_GET['a'])) && !token_check()){
	E('操作失败：非法令牌。');
}