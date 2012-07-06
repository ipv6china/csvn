<?php
include_once("view/header.php");
error_reporting (E_ALL);

define('DEBUG', false);
    
$group=$_GET['group'];
$dotFile='tmp/'.$group.'.dot';
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

//组节点
$groupname = $redis->hGet('group:'.$group.':info','groupname');
$content.="
	\"$group\" [shape=polygon  sides=6  color=green style=filled label=\"$group\\n$groupname\"];
";

//组成员
$users=$redis->sMembers('group:'.$group.':members');
foreach($users as $user){
	//用户节点
	$username = $redis->hGet('user:'.$user.':info','username');
	//如果没名字节点为红色
	if(empty($username))
		$userColor='color=red';
	else
		$userColor='color=orange';

	$content.="
        \"$user\" [shape=egg style=filled $userColor label=\"$user\\n$username\"];
	";

	//成员与组关系
    $content.="
        \"$user\" -> \"$group\" [color=black]; 
    ";

	//查看成员有无参加其他组
	$groups=$redis->sMembers('group:all');
	foreach($groups as $grp){
			if($redis->sIsMember('group:'.$grp.':members',$user)){
				if($grp==$group)continue;
				$grpname = $redis->hGet('group:'.$grp.':info','groupname');
				$content.="
					\"$grp\" [shape=polygon  sides=6  color=cyan style=filled label=\"$grp\\n$grpname\"];
				";
				
				//成员与组关系
    			$content.="
        			\"$user\" -> \"$grp\" [color=black]; 
    			";
			}

	}

}

//参加的项目
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

