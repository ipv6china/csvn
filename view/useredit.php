<?php
//Flydragon<ipv6china@comsenz.com
include_once("view/header.php");
include_once("models/useredit.php");
if($mod == 'edit'){
		$changePwInput='<tr><td>修改SVN密码</td><td><input type="checkbox" value="chpw" name=chpw></td></tr>';
}else{
		$changePwInput='';
}
print <<<EOT
   <script language="JavaScript">
        self.resizeTo(800,600);
    </script>
<h1>$pageTitle</h1>
<form action="/models/useredit.php" method=POST>
<table border=1 >
<tr><td>svn ID</td><td><input type="$idType" name="user" value="$user">$user</td></tr>
<tr><td>svn password</td><td><input type="text" name="password" value="$password"></td></tr>
$changePwInput
<tr><td>姓名</td><td><input type="text" name="username" value="$username"></td></tr>
<tr><td>部门</td><td><input type="text" name="department" value="$department"></td></tr>
<tr><td>联系方式</td><td><input type="text" name="contact" value="$contact"></td></tr>
EOT;
if($authUser == $superAdmin){
		print <<<EOT
<tr><td>管理密码</td><td><input type="text" name="authpw" value="$authpw"></td></tr>
<tr>
<td>修改管理密码<input type="checkbox" value="chadminpw" name="chadminpw"</td>
<td>解除管理<input type="checkbox" value="deladmin" name="deladmin"</td>
</tr>
EOT;
}
$ownGrps=implode('<br>',$redis->sMembers('user:'.$user.':owngrp'));
$ownProjs=implode('<br>',$redis->sMembers('user:'.$user.':ownproj'));
print <<<EOT
</table>
<input type="submit" >
</form>
<table border=1>
<tr>
	<td> 拥有组 </td>
	<td>$ownGrps</td>
</tr>
<tr>
	<td> 拥有项目 </td>
	<td> $ownProjs</td>
</tr>
</table>
EOT;
