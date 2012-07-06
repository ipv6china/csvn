<?php
//Flydragon<ipv6china@comsenz.com
error_reporting (E_ALL);
$pageTitle="可视化";
include_once("common.php");
include_once("view/header.php");
define('DEBUG', false);

$dotFile='tmp/'.time().'.dot';
$jpgFile=$dotFile.'.jpg';
$fp=fopen($dotFile, 'wb+');
if(!$fp) die('Can\'t open dotfile:$dotFile，die!');

$allUsers=$redis->sMembers('user:all');
$allGroups=$redis->sMembers('group:all');
$allProjects = $redis->sMembers('project:all');

$content="
digraph G{
/* global area */
nodesep=0.6
ranksep=0.6
splines=false
ratio=0.7
rankdir=LR 

/* users node */
";

//成员节点
foreach($allUsers as $user){
	$username = $redis->hGet('user:'.$user.':info','username');
	$content.="
		\"$user\" [shape=egg style=filled color=orange label=\"$user\\n$username\"];
	";
}

//组节点及关系
foreach($allGroups as $group){
	$groupname = $redis->hGet('group:'.$group.':info','groupname');
	$content.="
		\"$group\" [shape=polygon  sides=6  color=cyan style=filled label=\"$group\\n$groupname\"];
	";
	$allMembers=$redis->sMembers('group:'.$group.':members');
	foreach($allMembers as $user){
			$content.="
				\"$user\" -> \"$group\" [color=blue]; 
			";
	}
}

//项目节点及关系
foreach($allProjects as $project){
	$projectId = md5($project);
	$projectName = $redis->hGet('project:'.$projectId.':info', 'projectname');
	$content.="
		\"$projectId\" [shape=box  color=green style=filled label=\"$projectName\\n$project\"];
	";
	//只读用户
	$ros=$redis->sMembers('project:'.$projectId.':ro');
	if(!empty($ros)){
		foreach($ros as $ro){
			$content.="
					\"$ro\" -> \"$projectId\" [color=green];
			";
		}
	}
	//读写用户
	$rws=$redis->sMembers('project:'.$projectId.':rw');
	if(!empty($rws)){
		foreach($rws as $rw){
			$content.="
					\"$rw\" -> \"$projectId\" [color=red];
			";
		}
	}
	//只读组
	$gros=$redis->sMembers('project:'.$projectId.':gro');
	if(!empty($gros)){
		foreach($gros as $gro){
				$content.="
					\"$gro\" -> \"$projectId\" [color=green];
				";
		}
	}
	//读写组
	$grws=$redis->sMembers('project:'.$projectId.':grw');
	if(!empty($grws)){
		foreach($grws as $grw){
				$content.="
					\"$grw\" -> \"$projectId\" [color=red];
				";
		}
	}
}


$content.="
}
";

if(fwrite($fp, $content) === FALSE){
		die("无法写入文件$dotFile");
}
fclose($fp);

if(DEBUG){
	print <<<EOT
	<pre>
	$content
	</pre>
EOT;
}

exec("/usr/local/bin/dot -Tjpg -Kfdp -o $jpgFile $dotFile", $status);
print <<<EOT
点击放大:<br>
<a href=$jpgFile target=_blank><img src="$jpgFile" width=500 high=500></a>
EOT;

