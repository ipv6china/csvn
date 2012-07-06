<?php
//Flydragon<ipv6china@comsenz.com
include_once("view/header.php");
$project=$_GET['project'];

$redis->del('project:'.$project.':info');

$projectpath=$redis->hGet('md5index', $project);
$redis->sRem('project:all', $projectpath);

echo <<<EOF
    <script language="JavaScript">
	 	self.resizeTo(800,600);
		alert('修改已经提交，窗口将自动关闭。');
		window.close();
	</script>
EOF;

