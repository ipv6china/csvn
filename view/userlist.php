<?php
//Flydragon<ipv6china@comsenz.com
include_once("view/header.php");
print <<<EOT
<a href=user.php?mod=add target=_blank>添加新用户</a>  共有用户${userNum}位
<br />
<table border="0" cellspacing="1" bgcolor="#000000">
<tr bgcolor=#646464>
	<td> 权限</td>
	<td> ID </td>
	<td> 姓名 </td>
	<td> 部门 </td>
	<td> 联系方式 </td>
	<td> 操作 </td>

</tr>
EOT;
$tr=0;
sort($allUsers);
foreach($allUsers as $user){

		$username=$redis->hGet('user:'.$user.':info','username');
		$department=$redis->hGet('user:'.$user.':info','department');
		$contact=$redis->hGet('user:'.$user.':info','contact');
		//隔行换颜色
		if($tr%2==0)
			$trColor="#969696";
		else
			$trColor="#CCCCCC";
		
		//缺少描述的警示色
		if(empty($username))
			$warnColor="red";
		else
			$warnColor="black";
	
		if($redis->hExists('user:'.$user.':info','authpw')&& ''!=$redis->hGet('user:'.$user.':info','authpw')){
				$adminColor="red";
				$adm='*';
		}else{
				$adminColor="black";
				$adm='';
		}

		print <<<EOT
<tr bgcolor=$trColor>
	<td><a href=user.php?mod=showgrant&user=$user target=_blank>view</a></td>
	<td> <font color=$warnColor>$user</font> </td>
	<td> <font color=$adminColor>$adm $username</font> </td>
	<td> $department </td>
	<td> $contact </td>
	<td>
		<a href=user.php?mod=edit&user=$user target=_blank> <img src=images/edit.gif>编辑 </a> |
		<a href=user.php?mod=del&user=$user target=_blank><img src=images/delete.gif> 删除 </a> 
	</td>
</tr>
EOT;
	$tr++;

}

print <<<EOT
</table>
EOT;
