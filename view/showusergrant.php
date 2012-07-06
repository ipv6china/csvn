<?php
//Flydragon<ipv6china@comsenz.com
include_once("view/header.php");
error_reporting (E_ALL);

define('DEBUG', false);
    
$user=$_GET['user'];
$dotFile='tmp/'.$user.'.dot';
$jpgFile=$dotFile.'.jpg';
$fp=fopen($dotFile, 'wb+');
if(!$fp) die('Can\'t open dotfile:$dotFile，die!');

//global区  
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

//用户节点
$username = $redis->hGet('user:'.$user.':info','username');
$content.="
        \"$user\" [shape=egg style=filled color=orange label=\"$user\\n$username\"];
";

//直接参加的项目
	$projects=$redis->sMembers('project:all');
	foreach($projects as $project){
			$projectId=md5($project);
			$projectName = $redis->hGet('project:'.$projectId.':info', 'projectname');
			$projectPath = $redis->hGet('project:'.$projectId.':info', 'projectpath');
			if($redis->sIsMember('project:'.$projectId.':ro', $user)){
				$content.="
					\"$user\" -> \"$projectId\" [color=green,penwidth=25];
				";
				$drawProj=true;
			} elseif($redis->sIsMember('project:'.$projectId.':rw', $user)){
				$content.="
					\"$user\" -> \"$projectId\" [color=red,penwidth=15];
				";
				$drawProj=true;
			}else{
				$drawProj=false;
			}

			if($drawProj==true){
				$content.="
					\"$projectId\" [shape=box  color=grey style=filled label=\"$projectName\\n$projectPath\"];
				";
			}
	}

//参加的组
$groups=$redis->sMembers('group:all');
foreach($groups as $group){
	if(!$redis->sIsMember('group:'.$group.':members', $user))continue;
    $groupname = $redis->hGet('group:'.$group.':info','groupname');
	//组节点
    $content.="
        \"$group\" [shape=polygon  sides=6  color=cyan style=filled label=\"$group\\n$groupname\"];
    ";
	//成员与组关系
    $content.="
        \"$user\" -> \"$group\" [color=black]; 
    ";

	$projects=$redis->sMembers('project:all');
	foreach($projects as $project){
			$projectId=md5($project);
			$projectName = $redis->hGet('project:'.$projectId.':info', 'projectname');
			$projectPath = $redis->hGet('project:'.$projectId.':info', 'projectpath');
			if($redis->sIsMember('project:'.$projectId.':gro', $group)){
				$content.="
					\"$group\" -> \"$projectId\" [color=green,penwidth=9];
				";
				$drawProj=true;
			} elseif($redis->sIsMember('project:'.$projectId.':grw', $group)){
				$content.="
					\"$group\" -> \"$projectId\" [color=red,penwidth=2];
				";
				$drawProj=true;
			}else{
				$drawProj=false;
			}

			if($drawProj==true){
				$content.="
					\"$projectId\" [shape=box  color=grey style=filled label=\"$projectName\\n$projectPath\"];
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

