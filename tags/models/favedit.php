<?php
if(!$standalone){
		include_once("../view/header.php");
		include_once("../common.php");
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$url=$_POST['url'];
	$sitename=$_POST['sitename'];
	$tags=$_POST['tags'];
	$desc=$_POST['desc'];
	$favid=md5($url);
	
	if(!empty($_POST['favid'])&&$redis->sIsMember('Fav:all',$url)) die("已经添加过的网址");
		
	//设置
	$redis->hMset('Fav:'.$favid.':info', array('sitename' => $sitename, 'url' => $url, 'tags' => $tags, 'desc' => $desc));


	//最近添加列表
	if(!$redis->sIsMember('Fav:all',$url)){
		$redis->lPush('Fav:last',$url);
		$redis->lPush('sys.log','添加了网址:'.$url);
	}
			
	//添加url到所有url集合
	$redis->sAdd('Fav:all',$url);

	//添url的md5
	$ret=$redis->hExists('md5:index',$favid);
	if(!$ret){$redis->hSet('md5:index', $favid, $url);}

	$tags=explode(' ',$tags);
	//添加网址到标签集合，添加标签进入标签列表
	foreach($tags as $tag){
			$redis->sAdd('tag:'.$tag,$url);
			$redis->sAdd('tag:all',$tag);
	}


echo <<<EOF
    <script language="JavaScript">
	 	self.resizeTo(800,600);
		alert('修改已经提交，窗口将自动关闭。');
		window.close();
	</script>
EOF;
}else{
	$favid=@$_GET['favid'];
	//如果没有favid，为修改,否则为新添加
	if($favid){
		$url=$redis->hGet('md5:index',$favid);
		$sitename=$redis->hGet('Fav:'.$favid.':info','sitename');
		$tags=$redis->hGet('Fav:'.$favid.':info','tags');
		$desc=$redis->hGet('Fav:'.$favid.':info','desc');
	}else{
		$url=$sitename=$tags=$desc='';
	}
}
