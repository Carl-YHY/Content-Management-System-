<?php
define('NO_CHECK_LOGIN', true);
require './init.php';

//接收操作参数
$action = I('a', 'get', 'string');

//退出登录
if($action=='logout'){
	//清除Session
	unset($_SESSION['cms']['admin']);
	display([true, '您已经成功退出。']);
}

//处理表单
elseif($_POST){
	doPost();
}

//显示页面
display();

//处理表单
function doPost(){
	$name = I('name', 'post', 'html');
	$password = I('password', 'post', 'string');
	$captcha = I('captcha', 'post', 'string');
	//判断验证码
	if(!checkCode($captcha)){
		display([false, '登录失败：验证码输入有误。'], $name);
	}
	//根据用户名取出密码
	$data = db_fetch(DB_ROW, 'SELECT `id`,`name`,`password`,`salt` FROM `cms_admin` WHERE `name`=?', 's', $name);
	//判断用户名和密码
	if($data && (password($password, $data['salt']) == $data['password'])){
		//登录成功
		$_SESSION['cms']['admin'] = ['id'=>$data['id'], 'name'=>$data['name']];
		//跳转首页
		redirect('index.php');
	}
	//登录失败
	display([false, '登录失败：用户名或密码错误。'], $name);
}

//对验证码进行验证
function checkCode($code){
	$captcha = $_SESSION['cms']['captcha'];
	if(!empty($captcha)){
		unset($_SESSION['cms']['captcha']); //清除验证码，防止重复验证
		return strtoupper($captcha) == strtoupper($code); //不区分大小写
	}
	return false;
}

//显示页面
function display($msg=null, $name=''){
	require './view/login.html';
	exit;
}