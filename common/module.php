<?php
//功能模块

/**
 * 获取栏目列表
 * @param string $mode 索引方式（id 或 pid）默认返回两种格式
 * @return array 整理后的栏目数组
 */
function module_category($mode='all'){
	static $result = [];  //缓存查询结果
	//当第一次调用函数时，获取数据
	if(empty($result)){
		//先从文件中取出数据缓存
		$result = F('get', 'category');
		//如果缓存不存在，则到数据库中取出数据
		if(!$result){
			//定义数组用于保存结果
			$result = ['id'=>[], 'pid'=>[[]]];
			//到数据库中取出所有的栏目数据
			$data = db_fetch(DB_ALL, 'SELECT `id`,`name`,`pid`,`sort` FROM `cms_category` ORDER BY `pid` ASC, `sort` ASC');
			//整理数组格式，方便查找
			foreach($data as $v){
				//基于ID索引
				$result['id'][$v['id']] = $v;
				//基于PID索引
				$result['pid'][$v['pid']][$v['id']] = $v;
			}
			//将数据保存到缓存
			F('save', 'category', $result);
		}
	}
	return isset($result[$mode]) ? $result[$mode] : $result;
}

/**
 * 根据栏目ID获取栏目名称
 * @param int $id 栏目ID
 * @return string 栏目名称，如果不存在返回空字符串
 */
function module_category_name($id){
	$data = module_category('id');
	return isset($data[$id]) ? $data[$id]['name'] : '';
}

/**
 * 根据栏目ID获取子栏目
 * @param int $id 栏目ID
 * @return string 包括栏目ID、子栏目的字符串列表，用逗号分隔
 */
function module_category_sub($id){
	$data = module_category('pid');
	$sub = isset($data[$id]) ? array_keys($data[$id]) : [];
	array_unshift($sub, $id); //将$id放入数组开头
	return implode(',', $sub);
}

/**
 * 获取文章列表（仅前台）
 * @param int $cid 所属栏目ID，0表示所有
 * @param int $page 当前页码
 * @param int $limit 限制取出的个数
 * @return array 文章数组
 */
function module_article($cid=0, $page=1, $limit=12){
	$sql_where = ' WHERE ';
	$sql_where .= $cid ? '`cid` IN ('.module_category_sub($cid).')' : '1=1';
	$sql_where .= " AND `show`='yes' ";
	$sql_limit = ' LIMIT '.page_sql($page, $limit);
	//获取总页数
	$total = db_fetch(DB_COLUMN, "SELECT COUNT(*) FROM `cms_article` $sql_where");
	//查询并返回
	return[
		'title' => module_category_name($cid),
		'data' => db_fetch(DB_ALL, 'SELECT `id`,`title`,`time`,`author`,`thumb`,`description`'
			. " FROM `cms_article` $sql_where ORDER BY `id` DESC $sql_limit"),
		'page_html' => page_html($total, $page, $limit)
	];
}

/**
 * 根据ID获取文章内容（仅前台）
 */
function module_article_show($id){
	return db_fetch(DB_ROW, 'SELECT `cid`,`title`,`time`,`views`,`keywords`,`thumb`,`author`,`description`,`content` FROM `cms_article` WHERE `id`=? AND `show`=\'yes\'', 'i', $id);	
}

/**
 * 获取导航条栏目（仅前台）
 * @param string $limit 限制取出的个数
 * @return array 查询结果
 */
function module_category_nav($limit=4){
	$data = module_category('pid');
	return isset($data[0]) ? array_slice($data[0], 0, $limit) : [];
}

/**
 * 获取侧边栏 栏目（仅前台）
 * @param int $id 栏目ID
 * @return array 存在子栏目时，返回子栏目，不存在时返回同级栏目
 */
function module_category_sidebar($id=0){
	$data = module_category();
	//获取PID
	$pid = isset($data['id'][$id]) ? $data['id'][$id]['pid'] : 0;
	//如果有子栏目，返回子栏目，没有子栏目，返回同级栏目
	return isset($data['pid'][$id]) ? $data['pid'][$id] :
		(isset($data['pid'][$pid]) ? $data['pid'][$pid] : []);
}

/**
 * 获取栏目的顶级栏目ID
 */
function module_category_top($id){
	$data = module_category('id');
	$pid = isset($data[$id]) ? $data[$id]['pid'] : 0;
	return $pid ? : $id;
}

/**
 * 获取浏览历史（仅前台）
 * @param array $current 浏览过的文章ID，为false表示不保存
 * @param int $limit 限制取出的个数
 * return array 历史记录数组
 */
function module_history($current=false, $limit=10){
	$result = []; //保存历史记录数组
	//如果Cookie中存在历史记录，先取出记录
	if(isset($_COOKIE['cms_history'])){
		//获取Cookie，将字符串分割成数组，并限制分割次数
		$result = explode(',', $_COOKIE['cms_history'], $limit);
		//将数组中的每个元素转换为整数
		$result = array_map('intval', $result);
	}
	//将当前文章ID保存到历史记录中
	if($current){
		//如果当前ID在数组中已经存在，则删除
		if(false !== ($del = array_search($current, $result))){
			unset($result[$del]);
		}
		//将当前文章ID添加到数组开始
		array_unshift($result, $current);
		//当数组元素达到限制时，删除最后一个元素
		if(isset($result[$limit])){
			unset($result[$limit]);
		}
	}
	if(!empty($result)){
		//保存到Cookie并返回
		$sql_in = implode(',', $result);
		setcookie('cms_history', $sql_in);
		return db_fetch(DB_ALL, "SELECT `id`,`title` FROM `cms_article` WHERE `id` IN($sql_in) AND `show`='yes' ORDER BY FIELD(`id`,$sql_in)");
	}
	return [];
}

/**
 * 获取最热文章（仅前台）
 */
function module_hot($limit=10){
	return db_fetch(DB_ALL, "SELECT `id`,`title` FROM `cms_article` WHERE `show`='yes' ORDER BY `views` DESC LIMIT 0,$limit");
}