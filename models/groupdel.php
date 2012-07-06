<?php
include_once("view/header.php");
$group=$_GET['group'];

$redis->del('group:'.$group.':info');

$redis->sRem('group:all', $group);

echo <<<EOF
    <script language="JavaScript">
	 	self.resizeTo(800,600);
		alert('修改已经提交，窗口将自动关闭。');
		window.close();
	</script>
EOF;

