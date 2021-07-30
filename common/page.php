<?php
//分页函数

//获取SQL分页 Limit
function page_sql($page, $size){
	return ($page-1) * $size . ',' . $size;
}

//生成URL参数
function page_url(){
	$params = $_GET;
	unset($params['page']);
	return http_build_query($params);	
}

//获取分页HTML（简单效果）
function page_html_simple($total, $page, $size){
	//计算总页数
	$maxpage = max(ceil($total/$size), 1);
	//如果不足2页，则不显示分页导航
	if($maxpage <= 1) return '';
	//获取URL参数字符串
	$url = page_url();
	$url = $url ? "?$url&page=" : '?page=';
	//拼接 首页
	$first = "<a href=\"{$url}1\">首页</a>";
	//拼接 上一页
	$prev = ($page==1) ? '<span>上一页</span>' :
			'<a href="'.$url.($page-1).'">上一页</a>';
	//拼接 下一页
	$next = ($page==$maxpage) ? '<span>下一页</span>' :
			'<a href="'.$url.($page+1).'">下一页</a>';
	//拼接 尾页
	$last = "<a href=\"{$url}{$maxpage}\">尾页</a>";
	//组合最终样式
	return "$first $prev $next $last 当前为：$page/$maxpage";
}

//获取分页HTML
function page_html($total, $page, $size, $num=5){
	$page = max((int)$page, 1);             //当前访问的页码，最低为1
	$maxpage = max(ceil($total/$size), 1);  //计算总页数
	$num = floor($num/2);                   //计算当期页前后显示的相关链接个数
	$url = page_url();		                //获取URL参数字符串
	//拼接URL
	$url = $url ? "?$url&page=" : '?page=';
	$html = ''; //保存拼接结果
	//生成首页、上一页
	if($page > 1){
		$html .= "<a href=\"{$url}1\">首页</a>";
		$html .= '<a href="'.$url.($page-1).'">上一页</a>';
	}else{
		$html .= '<span>首页</span>';
		$html .= '<span>上一页</span>';
	}
	//生成分页导航
	$start = $page - $num;
	$end = $page + $num;
	if($start < 1){
		$end = $end+(1-$start);
		$start = 1;
	}
	if($end > $maxpage){
		$start = $start-($end-$maxpage);
		$end = $maxpage;
	}
	($start < 1) && $start = 1;
	($start > 1) && $html .= '<i>...</i>';
	for($i=$start; $i<=$end; ++$i){
		if($i == $page){
			$html .= "<a href=\"{$url}{$i}\" class=\"curr\">$i</a>";
		} else {
			$html .= "<a href=\"{$url}{$i}\">$i</a>";
		}
	}
	($end < $maxpage) && $html .= '<i>...</i>';
	//生成下一页、尾页
	if($page == $maxpage){
		$html .= '<span>下一页</span>';
		$html .= '<span>尾页</span>';
	}else{
		$html .= '<a href="'.$url.($page+1).'">下一页</a>';
		$html .= "<a href=\"{$url}{$maxpage}\">尾页</a>";
	}
	//返回结果
	return $html;
}