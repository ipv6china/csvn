<?php
//Flydragon<ipv6china@comsenz.com
include_once("view/header.php");
error_reporting (E_ALL);

define('DEBUG', false);
    
$projectId=$_GET['project'];
$dotFile='tmp/'.$projectId.'.dot';
$jpgFile=$dotFile.'.jpg';
$fp=fopen($dotFile, 'wb+');
if(!$fp) die('Can\'t open dotfile:$dotFile，die!');

   
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

//项目节点
$projectName = $redis->hGet('project:'.$projectId.':info', 'projectname');
$projectPath = $redis->hGet('project:'.$projectId.':info', 'projectpath');
$content.="
        \"$projectId\" [shape=box  color=grey style=filled label=\"$projectName\\n$projectPath\"];
    ";

//用户节点
$ros=$redis->sMembers('project:'.$projectId.':ro');//只读
$rws=$redis->sMembers('project:'.$projectId.':rw');//读写
$users=array_merge($ros,$rws);
foreach($users as $user){
    $username = $redis->hGet('user:'.$user.':info','username');
	 //如果没名字节点为红色
    if(empty($username))
        $userColor='color=red';
    else
        $userColor='color=orange';

    $content.="
        \"$user\" [shape=egg style=filled $userColor label=\"$user\\n$username\"];
    ";
}
//用户权限关系
    //只读用户
    if(!empty($ros)){
        foreach($ros as $ro){
            $content.="
                    \"$ro\" -> \"$projectId\" [color=green,penwidth=25];
            ";
        }
    }
    //读写用户
    if(!empty($rws)){
        foreach($rws as $rw){
            $content.="
                    \"$rw\" -> \"$projectId\" [color=red,penwidth=15];
            ";
        }
    }

//组节点
$gros=$redis->sMembers('project:'.$projectId.':gro');
$grws=$redis->sMembers('project:'.$projectId.':grw');
$groups=array_merge($gros, $grws);
foreach($groups as $group){
    $groupname = $redis->hGet('group:'.$group.':info','groupname');
   //如果没名字节点为红色
    if(empty($groupname))
        $groupColor='color=red';
    else
        $groupColor='color=orange';

    $content.="
        \"$group\" [shape=polygon  sides=6  $groupColor style=filled label=\"$group\\n$groupname\"];
    ";
    $allMembers=$redis->sMembers('group:'.$group.':members');
    foreach($allMembers as $user){
	    $username = $redis->hGet('user:'.$user.':info','username');
     	//如果没名字节点为红色
    	if(empty($username))
        	$userColor='color=red';
    	else
        	$userColor='color=orange';

   		$content.="
       		 \"$user\" [shape=egg style=filled $userColor label=\"$user\\n$username\"];
    	";
            $content.="
                \"$user\" -> \"$group\" [color=black]; 
            ";
    }
}

//组权限关系
    //只读组
    if(!empty($gros)){
        foreach($gros as $gro){
                $content.="
                    \"$gro\" -> \"$projectId\" [color=green,penwidth=9];
                ";
        }
    }
    //读写组
    if(!empty($grws)){
        foreach($grws as $grw){
                $content.="
                    \"$grw\" -> \"$projectId\" [color=red,penwidth=2];
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

