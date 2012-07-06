<?php
include_once("view/header.php");

print <<<EOT
<h4>最近添加的收藏</h4>
<a href=fav.php?mod=add target=_blank>添加新收藏</a> 
<br />
<table border="0" cellspacing="1" bgcolor="#000000">
<tr bgcolor=#a2c2ff>
	<td> 网站名 </td>
	<td> 网站URL </td>
	<td> tag列表 </td>
	<td> 操作 </td>

</tr>
EOT;
$tr=0;
$lastFavs=$redis->lRange('Fav:last',0,15);
foreach($lastFavs as $fav){
	//隔行换颜色
	if($tr%2==0)
		$trColor="#e8f0ff";
	else
		$trColor="#f4f9ff";
						    

	$fav=md5($fav);

	if(!$redis->exists('Fav:'.$fav.':info')) continue;
	$tags=$redis->hGet('Fav:'.$fav.':info','tags');
	$url=$redis->hGet('Fav:'.$fav.':info','url');
	$favid=md5($url);
	$sitename=$redis->hGet('Fav:'.$fav.':info','sitename');

	print <<<EOT
<tr bgcolor=$trColor>
	<td> $sitename </td>
	<td> <a href=$url target=_blank>$url</a> </td>
	<td> $tags</td>
	<td>
		<a href=fav.php?mod=edit&favid=$favid target=_blank><img src=images/edit.gif> 编辑 </a> |
		<a href=fav.php?mod=del&favid=$favid target=_blank> <img src=images/delete.gif>删除 </a> 
	</td>
</tr>
EOT;
$tr++;
}


print <<<EOT
</table>
<hr>
<h4>按标签浏览</h4>
<table border=0>
<tr>
EOT;

$word=(!empty($_GET['word']))?$_GET['word']:'';

	//显示tag标签
	$count=0;
	$alltags = $redis->sMembers('tag:all');
	$normal="</font>";

	foreach ($alltags as $tag){
			if($count==8){
					echo "</tr><tr>";
					$count=0;
			}
			if($tag==$word){
					$large="<font size=5>";
					$normal="</font>";
			} else{
					$large="";
					$normal="";
			}
			$tagNu=$redis->sCard('tag:'.$tag);
			print <<<EOT
				<td><a href=fav.php?mod=list&word=$tag>$large $tag</a>$normal<font size=2 color=gray>($tagNu)</font>&nbsp;&nbsp;</td>
EOT;
			
			//$large=" ";$normal=" ";
			$count++;
	}
	//显示选定标签的网站列表
	print <<<EOT
</tr>
</table>
<table border="0" cellspacing="1" bgcolor="#000000">
<tr bgcolor=#a2c2ff>
	<td> 网站名 </td>
	<td> 网站URL </td>
	<td> 简介 </td>
EOT;
if('flydragon'==$admin || $_SERVER['REMOTE_ADDR']==$whiteIp){
	echo "	<td> 操作 </td>";
}
print <<<EOT
</tr>
EOT;
$tr=0;
	$tagfavs=$redis->sMembers('tag:'.$word);
	foreach($tagfavs as $fav){
		$fav=md5($fav);
		if(!$redis->exists('Fav:'.$fav.':info')) continue;
		$url=$redis->hGet('Fav:'.$fav.':info','url');
		$favid=md5($url);
		$sitename=$redis->hGet('Fav:'.$fav.':info','sitename');
		$desc=$redis->hGet('Fav:'.$fav.':info','desc');
		//隔行换颜色
		if($tr%2==0)
			$trColor="#e8f0ff";
		else
			$trColor="#f4f9ff";

		print <<<EOT
			<tr bgcolor=$trColor>
				<td> $sitename </td>
				<td> <a href=$url target=_blank>$url </a></td>
				<td> $desc </td>
EOT;
		if('flydragon'==$admin || $_SERVER['REMOTE_ADDR']==$whiteIp){
			print <<<EOT
				<td>
				<a href=fav.php?mod=edit&favid=$favid target=_blank> <img src=images/edit.gif>编辑 </a> |
				<a href=fav.php?mod=del&favid=$favid target=_blank> <img src=images/delete.gif>删除 </a> 
				</td>
EOT;
		}
print <<<EOT
			</tr>
EOT;
		$tr++;
	}
	print <<<EOT
</table>
EOT;

