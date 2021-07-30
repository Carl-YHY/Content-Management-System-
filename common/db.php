<?php
//数据库相关函数

//定义相关常量
define('DB_ALL', 0);      //获得全部结果
define('DB_ROW', 1);	  //获得1行结果
define('DB_COLUMN', 2);   //获得1个结果
define('DB_AFFECTED', 3); //获得受影响的行数
define('DB_LASTID', 4);   //获得最后插入的ID 

/**
 * 执行SQL并获取查询结果
 * @param int $mode 模式（DB_ALL，DB_ROW，DB_COLUMN）
 * @param string $sql 执行的SQL语句
 * @param string $type 预处理数据格式
 * @param string $data 预处理数据内容
 * @return 查询结果
 */
function db_fetch($mode, $sql, $type='', $data=[]){
	$stmt = db_query($sql, $type, $data);
	//获取结果集
	$result = mysqli_stmt_get_result($stmt);
	//根据指定格式返回数据
	switch($mode){
		case DB_ROW:	return mysqli_fetch_assoc($result);
		case DB_COLUMN:	return current((array)mysqli_fetch_row($result));
		default:		return mysqli_fetch_all($result, MYSQLI_ASSOC);
	}
}
/**
 * 执行SQL语句（支持批量执行）
 * @param int $mode 模式（DB_AFFECTED，DB_LASTID）
 * @param string $sql 执行的SQL语句
 * @param string $type 预处理数据格式
 * @param string $data 预处理数据内容
 * @return 返回结果
 */
function db_exec($mode, $sql, $type='', $data=[]){
	$stmt = db_query($sql, $type, $data);
	//根据指定格式返回数据
	switch($mode){
		case DB_LASTID: return mysqli_stmt_insert_id($stmt);
		default:		return mysqli_stmt_affected_rows($stmt);
	}
}
//通过预处理方式执行SQL
function db_query($sql, $type='', $data=[]){
	//获取数据库连接
	$link = db_connect();
	//预处理SQL语句
	if(!$stmt = mysqli_prepare($link, $sql)){
		E('数据库操作失败。', mysqli_error($link)."\nSQL语句：".$sql);
	}
	//无参数绑定时，直接执行
	if($data == []){
		mysqli_stmt_execute($stmt);
	}else{
		//批量处理
		$data = (array)$data;							//如果不是数组，强制转换为数组
		is_array(current($data)) || $data = [$data];	//自动识别批量模式
		$params = array_shift($data);					//准备待绑定的变量
		db_bind_param($stmt, $type, $params);			//参数绑定
		mysqli_stmt_execute($stmt);						//执行第1个
		foreach($data as $row){							//批量执行剩余的
			foreach($row as $k=>$v){
				$params[$k] = $v;	//变更参数值
			}
			mysqli_stmt_execute($stmt);
		}
	}
	return $stmt;
}

//自动完成参数绑定
function db_bind_param($stmt, $type, &$data){
	$params = [$stmt, $type];		//保存预处理参数
	foreach($data as &$params[]){}	//创建引用
	call_user_func_array('mysqli_stmt_bind_param', $params);  //参数绑定
}

//连接数据库
function db_connect(){
	static $link = null; //保存数据库连接
	if(!$link){
		if(!$link = call_user_func_array('mysqli_connect', C('DB_CONNECT'))) {
			E('数据库连接失败。',mysqli_connect_error());
		}
		mysqli_set_charset($link, C('DB_CHARSET'));
	}
	return $link;
}
//对LIKE条件进行转义
function db_escape_like($like){
	return strtr($like, ['%'=>'\%', '_'=>'\_', '\\'=>'\\\\']); 
}