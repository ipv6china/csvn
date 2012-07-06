<?php
//Flydragon<ipv6china@comsenz.com
$pageTitle='管理者工具';
include_once("common.php");
include_once("view/header.php");

if($authUser != $superAdmin) die("没有权限");

print <<<EOT
<h2>编外管理警告</h2>
<font size=4 color=red>
EOT;

$_allDBusers=$redis->Keys('user:*');
foreach($_allDBusers as $u){
        $_user=explode(':',$u);
        $user=$_user[1];

        if(!$redis->sIsMember('user:all', $user)&&$redis->hExists('user:'.$user.':info', 'authpw'))
                echo "$user<br />";

}

echo "</font><br>";

