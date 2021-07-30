<?php
define('NO_CHECK_LOGIN', true);
require './init.php';
require COMMON_PATH.'/captcha.php'; //载入验证码函数

//生成验证码
$code = captcha_create();

//输出验证码
captcha_show($code);

//将验证码保存到Session中
$_SESSION['cms']['captcha'] = $code;