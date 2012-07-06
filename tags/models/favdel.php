<?php
include_once("view/header.php");
$favid=$_GET['favid'];
$url=$redis->hGet('md5:index', $favid);
$tags=$redis->hGet('Fav:'.$favid.':info', $tags);

foreach($tags as $tag){
	//从标签集合里删除此url
	$redis->sRem('tag:'.$tag, $url);
	//如果标签里没网址了，就删除标签
	if(0==size($redis->sMembers('tag:'.$tag))){
		$redis->del('tag:'.$tag);
		$redis->sRem('tag:all',$tag);
	}
}

$redis->sRem('Fav:all',$url);
$redis->sRem('md5:index',$favid);
$redis->del('Fav:'.$favid.':info');

$redis->lPush('sys.log','删除了网址:'.$url);

echo <<<EOF
    <script language="JavaScript">
        self.resizeTo(800,600);
        alert('修改已经提交，窗口将自动关闭。');
        window.close();
    </script>
EOF;


