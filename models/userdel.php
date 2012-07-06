<?php
//Flydragon<ipv6china@comsenz.com
include_once("view/header.php");
$user=$_GET['user'];

$redis->del('user:'.$user.':info');

$redis->sRem('user:all', $user);

echo <<<EOF
    <script language="JavaScript">
	 	self.resizeTo(800,600);
		alert('修改已经提交，窗口将自动关闭。');
		window.close();
	</script>
EOF;

