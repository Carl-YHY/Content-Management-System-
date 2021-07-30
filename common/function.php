<?php

//配置文件操作
function C($name){
	static $config = null; //保存项目中的设置
	if(!$config){          //函数首次被调用时载入配置文件
		$config = require COMMON_PATH.'config.php';
	}
	return isset($config[$name]) ? $config[$name] : '';
}

/**
 * 接收变量，并进行过滤
 * @param string $var 变量名
 * @param array|string $method 待过滤的数组，或表示超全局变量的字符串
 * @param string $type 过滤类型
 * @param mixed $def 默认值
 * @return 过滤后的结果
 */
function I($var, $method='post', $type='html', $def=''){
	switch($method){
		case 'get':    $method = $_GET;    break;
		case 'post':   $method = $_POST;   break;
		case 'server': $method = $_SERVER; break;
		case 'cookie': $method = $_COOKIE; break;
	}
	$value = isset($method[$var]) ? $method[$var] : $def;
	switch($type){
		case 'string': //字符串 不进行过滤
			$value = is_string($value) ? $value : '';
		break;
		case 'html': //字符串 进行HTML转义
			$value = is_string($value) ? toHTML($value) : '';
		break;
		case 'int': //整数
			$value = (int)$value;
		break;
		case 'id': //无符号整数
			$value = max((int)$value, 0);
		break;
		case 'page': //页码
			$value = max((int)$value, 1);
		break;
		case 'float': //浮点数
			$value = (float)$value;
		break;
		case 'bool': //布尔型
			$value = (bool)$value;
		break;
		case 'array': //数组型
			$value = is_array($value) ? $value : [];
		break;
	}
	return $value;
}

//缓存文件读写
function F($method, $name, $data=''){
	$path = COMMON_PATH."cache/$name.dat";
	switch($method){
		case 'get':
			return is_file($path) ? unserialize(file_get_contents($path)) : null;
		case 'save':
			return file_put_contents($path, serialize($data));
		case 'unset':
			is_file($path) && unlink($path);
		break;
	}
}

//字符串转HTML
function toHTML($str){
	$str = trim(htmlspecialchars($str, ENT_QUOTES));
	return str_replace(' ', '&nbsp;', $str);
}

//密码加密
function password($password, $salt){
	return md5(md5($password).$salt);
}

//生成密钥
function salt(){
	return substr(uniqid(), -6);
}

//生成令牌
function token_get(){
	if(isset($_SESSION['cms']['token'])){
		$token = $_SESSION['cms']['token'];
	}else{
		$token = md5(microtime(true));
		$_SESSION['cms']['token'] = $token;
	}
	return $token;
}

//验证令牌
function token_check($token=''){
	if(!$token){ //自动取出token
		$token = I('token', 'get', 'string');
	}
	return token_get() == $token;
}

//遇到致命错误，输出错误信息并停止运行
function E($msg, $debug=''){
	$msg .= APP_DEBUG ? $debug : '';
	exit('<pre>'.htmlspecialchars($msg).'</pre>');
}

//重定向
function redirect($url){
	header("Location:$url");
	exit;
}

//提示信息
function tips($msg=null){
	if(!$msg){
		return '';	//没有提示信息时直接返回空字符串
	}
	return $msg[0] ? "<div>$msg[1]</div>" : "<div class=\"error\">$msg[1]</div>";
}

//删除文件
function del_file($file_path){
	if(is_file($file_path)){
		unlink($file_path);
	}
}

/**
 * 判断上传文件是否成功
 * @param array $up 上传文件的$_FILES数组元素
 * @return 成功返回true，失败返回错误信息
 */
function check_upload($up){
	switch($up['error']){
		case 0: return is_uploaded_file($up['tmp_name']) ? true : '非法文件';
		case 1: return '文件大小超过了服务器设置的限制！';
		case 2: return '文件大小超过了表单设置的限制！';
		case 3: return '文件只有部分被上传！';
		case 4: return '没有文件被上传！';
		case 6: return '上传文件临时目录不存在！';
		case 7: return '文件写入失败！';
		default: return '未知错误';
	}
}