<?php
//Flydragon<ipv6china@comsenz.com
include_once("view/header.php");
include_once("models/groupedit.php");
print <<<EOT
<h1>$pageTitle</h1>
<form action="/models/groupedit.php" method=POST>
<table border="1">
<tr><td>组 ID</td><td><input type="$idType" name="group" value="$group">$group</td></tr>
<tr><td>用途</td><td><input type="text" name="groupname" value="$groupname"></td></tr>
<tr><td>部门</td><td><input type="text" name="department" value="$department"></td></tr>
<tr><td>组长ID</td><td><input type="text" name="owner" value="$owner"></td></tr>
<tr><td>联系方式</td><td><input type="text" name="contact" value="$contact"></td></tr>
</table><br />
成员列表:<br />
<table border="1" >
<tr>
EOT;
$users=$redis->sMembers('group:'.$group.':members');
$br=0;
foreach($users as $user){
		if($br==5){
				echo "\n</tr>";
				$br=0;
				echo "\n<tr>";
		}
		$username=$redis->hGet('user:'.$user.':info', 'username');
		print <<<EOT

				<td><input type="checkbox" checked value=$user name=users[]>$username$user </td>
EOT;
		$br++;

}
$trColor="#DDDDDD";
print <<<EOT
</tr>
</table>
<input type="submit" name=modify value="提交变更">
</form>
EOT;

//编辑模式时显示成员列表
if(!empty($group)){
	print <<<EOT
<hr>
<form action="/models/groupedit.php" method=POST>
<input type="submit" name=addmember value="添加新成员">
<table border="0" cellspacing="1" bgcolor="#000000">
<tr bgcolor=$trColor>\n
EOT;
	$freeUsers=$redis->sDiff('user:all', 'group:'.$group.':members');
	//按英文姓名排序
	sort($freeUsers);
	//换行变量
	$br=0;
	foreach($freeUsers as $user){
			//换行
			if($br==5){
					echo "\n</tr>\n";
					$br=0;
					if($trColor=="#DDDDDD"){
							$trColor="#c0c0c0";
					}else{
							$trColor="#DDDDDD";
					}
					echo "\n<tr bgcolor=$trColor>\n";
			}


			$username=$redis->hGet('user:'.$user.':info', 'username');
			if($redis->sIsMember('group:'.$group.':members',$user)==true)
					$checked='checked';
			else
		   			$checked='';

			print <<<EOT
		<td><input type="checkbox" $checked value=$user name=addusers[]>$username$user </td>\n
EOT;
			$br++;
	}
	print <<<EOT
</tr>
</table>
<input type="hidden" name="group" value="$group">
</form>
EOT;
}
