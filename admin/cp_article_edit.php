<?php
require './init.php';

//接收文章ID
$id = I('id', 'get', 'id');

//处理表单
if($_POST){
	doPost($id);
}

//如果 $id 为0，显示添加页面，否则，显示修改页面
display(null, $id);

//---------------------------------

//处理表单
function doPost($id){
	//接收变量
	$input = [
		'cid' => I('cid', 'post', 'id'),					//栏目ID
		'title' => I('title', 'post', 'html'),				//标题
		'author' => I('author', 'post', 'html'),			//作者
		'show' => I('save', 'post', 'bool') ? 'no' : 'yes', //是否发布
		'content' => I('content', 'post', 'string'),		//内容
		'keywords' => I('keywords', 'post', 'html'),		//关键字
		'description' => I('description', 'post', 'html'),	//摘要
	];
	//如果存在ID，则查出原来的thumb
	$input['thumb'] = $id ? db_fetch(DB_COLUMN, 'SELECT `thumb` FROM `cms_article` WHERE `id`=?', 'i', $id) : '';
	//有图片上传时，处理上传封面
	if(isset($_FILES['thumb']) && $_FILES['thumb']['error'] != 4){
		//判断上传是否有错误
		if(true !== ($error = check_upload($_FILES['thumb']))){
			display([false, '文件上传失败：'.$error], $id, $input);
		}
		//载入图像处理函数
		require COMMON_PATH.'image.php';
		//为上传文件生成缩略图并保存
		list($thumb_flag, $thumb_result) = image_thumb(
			IMAGE_SCALE,					//等比例缩放
			$_FILES['thumb']['tmp_name'],	//原图路径
			780,							//目标宽度
			220,							//目标高度
			['base_path'=>UPLOAD_PATH]		//上传目录
		);
		//判断缩略图是否成功
		if($thumb_flag){
			del_file(UPLOAD_PATH.$input['thumb']);	//删除原图
			$input['thumb'] = $thumb_result;		//保存新图文件路径
		}else{
			display([false, $thumb_result], $id, $input);	
		}
	}
	if($id){
		//修改数据
		$input['id'] = $id;
		db_query('UPDATE `cms_article` SET `cid`=?,`title`=?,`author`=?,`show`=?,`content`=?,`keywords`=?,`description`=?,`thumb`=? WHERE `id`=?', 'isssssssi', $input);
		display([true, '修改成功。<a href="cp_article.php">返回列表</a>'], $id);
	}else{
		//添加数据
		$input['time'] = date('Y-m-d H:i:s');
		$add_id = db_exec(DB_LASTID, 'INSERT INTO `cms_article` (`cid`,`title`,`author`,`show`,`content`,`keywords`,`description`,`thumb`,`time`) VALUES(?,?,?,?,?,?,?,?,?)', 'issssssss', $input);
		display([true, '添加成功。<a href="cp_article_edit.php?id='.$add_id.'">立即修改</a><a href="cp_article.php">返回列表</a>']);
	}
}

//显示页面
function display($msg=null, $id=0, $data=[]){
	if($id && empty($data)){
		//根据ID查出原有的记录
		if(!$data = db_fetch(DB_ROW, 'SELECT `cid`,`title`,`author`,`show`,`content`,`keywords`,`description`,`thumb` FROM `cms_article` WHERE `id`=?', 'i', $id)){
			E('数据不存在！');
		}
	}else{
		//合并模板变量（初始值）
		$data = array_merge([
			'cid' => 0,
			'title' => '',
			'author' => '',
			'show' => 'yes',
			'content' => '',
			'keywords' => '',
			'description' => '',
			'thumb' => ''
		],$data);
	}
	//获取栏目数据
	$category = module_category('pid');
	//载入HTML模板
	require './view/article_edit.html';
	exit;
}