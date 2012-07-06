<?php
//Flydragon<ipv6china@comsenz.com
$pageTitle="发布配置文件";
include_once("common.php");
include_once("view/header.php");
define('DEBUG',false);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	/////// httpasswd
	$fp=fopen($releaseDir.'/httpasswd','wb+');
	if(!$fp) die("Can't open httpasswd file!");

	$pwContent='';
	$allUsers=$redis->sMembers('user:all');
	sort($allUsers);
	foreach($allUsers as $user){
			$passwd=$redis->hGet('user:'.$user.':info', 'password');
			$username=$redis->hGet('user:'.$user.':info', 'username');
			$department=$redis->hGet('user:'.$user.':info', 'department');
			$pwContent.="# $username $department\n$user:$passwd\n\n";
	}

	//写入文件
	if(fwrite($fp, $pwContent) === FALSE){
        	die("无法写入文件httpasswd");
	}
	fclose($fp);

	if(DEBUG){
    	print <<<EOT
<h2>密码文件httpasswd</h2>
    <pre>
$pwContent
    </pre>
EOT;
}

	////// authz
	$fp=fopen($releaseDir.'/authz', 'wb+');
	if(!$fp) die("Can't open httpasswd file!");
	
	$authContent='';
	$authContent.="[groups]\n";
	$allGroups=$redis->sMembers('group:all');
	sort($allGroups);
	//组列表
	foreach($allGroups as $group){
		$members=implode(',',$redis->sMembers('group:'.$group.':members'));
		$groupname=$redis->hGet('group:'.$group.':info', 'groupname');
		$department=$redis->hGet('group:'.$group.':info', 'department');
		$authContent.="# $groupname $department\n$group = $members\n";
	}

	$authContent.="\n";
	function createPath($projectpath){
		$count = substr_count($projectpath, '/');
		if($count == 1)
				$projectpath=str_replace('/','',$projectpath).':/';
		else {
				$projectpath=preg_replace("/\/(\w+)\/(.*)/i","\${1}:/\$2",$projectpath);
		}
	
		$r="[$projectpath]";
		return $r;
	}
	//项目及授权列表
	$allProjects=$redis->sMembers('project:all');
	sort($allProjects);
	foreach($allProjects as $projectpath){
			$project=md5($projectpath);
			$projectname=$redis->hGet('project:'.$project.':info', 'projectname');
			$department=$redis->hGet('project:'.$project.':info', 'department');
			$authContent .= "# $projectname $department \n". createPath($projectpath)."\n";
			$ros=$redis->sMembers('project:'.$project.':ro');
			$grws=$redis->sMembers('project:'.$project.':grw');
			$gros=$redis->sMembers('project:'.$project.':gro');
			$rws=$redis->sMembers('project:'.$project.':rw');
			foreach($grws as $grw)
				$authContent.="@$grw = rw\n";
			foreach($gros as $gro)
				$authContent.="@$gro = r\n";
			foreach($rws as $rw)
				$authContent.="$rw = rw\n";
			foreach($ros as $ro)
				$authContent.="$ro = r\n";
		
			$authContent.="* = \n\n";
	
	}

	//写入文件
	if(fwrite($fp, $authContent) === FALSE){
        	die("无法写入文件authz");
	}
	fclose($fp);
	echo "<h1> 发布成功</h1>";

	if(DEBUG){
    	print <<<EOT
	<h2>认证文件authz</h2>
    <pre>
$authContent
    </pre>
EOT;
	}

	backupDatabase($redis,$_POST['buildmsg']);
} else {
	print <<<EOT
<h1>$pageTitle</h1>
<form action="build.php" method=POST>
发布原因:<input type="text" name="buildmsg" value="" style="width:200px;">
<input type="submit" >
</form>
EOT;

}

