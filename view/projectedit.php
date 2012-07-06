<?php
//Flydragon<ipv6china@comsenz.com
include_once("view/header.php");
include_once("models/projectedit.php");
print <<<EOT
<h1>$pageTitle</h1>
<form action="/models/projectedit.php" method=POST>
<table border="1">
<tr><td>项目路径</td><td><input type="text" name="projectpath" value="$projectpath" style="width:500px;"></td></tr>
<tr><td>项目名</td><td><input type="text" name="projectname" value="$projectname"></td></tr>
<tr><td>部门</td><td><input type="text" name="department" value="$department"></td></tr>
<tr><td>项目组长</td><td><input type="text" name="owner" value="$owner"></td></tr>
<tr><td>联系方式</td><td><input type="text" name="contact" value="$contact"></td></tr>
</table>

<hr>
只读权限用户:<br />
<table border="1">
<tr>
EOT;
$ros=$redis->sMembers('project:'.$project.':ro');
$rws=$redis->sMembers('project:'.$project.':rw');
//列出只读用户
$br=0;
foreach($ros as $user){
            if($br==5){
                    echo "\n</tr>";
                    $br=0;
                    echo "\n<tr>";
            }   
            $username=$redis->hGet('user:'.$user.':info', 'username');
                    $checked='checked';

            print <<<EOT
                       <td><input type="checkbox" $checked value=$user name=rousers[]>$username$user </td>
EOT;
            $br++;
}

// 读写权限用户
print <<<EOT
</tr>
</table>
<br />
读写权限用户:<br />
<table border="1">
<tr>
EOT;
//列出读写用户
$br=0;
foreach($rws as $user){
            if($br==5){
                    echo "\n</tr>";
                    $br=0;
                    echo "\n<tr>";
            }   
            $username=$redis->hGet('user:'.$user.':info', 'username');
                    $checked='checked';

            print <<<EOT
		<td> <input type="checkbox" $checked value=$user name=rwusers[]>$username$user </td>
EOT;
            $br++;
}

$gros=$redis->sMembers('project:'.$project.':gro');
$grws=$redis->sMembers('project:'.$project.':grw');
// 只读权限组
print <<<EOT
</tr>
</table>
<br />
只读权限组:<br />
EOT;
//列出读权限组
$br=0;
foreach($gros as $group){
            if($br==8){
                    echo "\n<br />";
                    $br=0;
            }   
            $groupname=$redis->hGet('group:'.$group.':info', 'groupname');
                    $checked='checked';

            print <<<EOT
                        <input type="checkbox" $checked value=$group name=rogroups[]>$groupname$group |
EOT;
            $br++;
}

// 读写权限组
print <<<EOT
<br />
读写权限组:<br />
EOT;
//列出读写权限组
$br=0;
foreach($grws as $group){
            if($br==8){
                    echo "\n<br />";
                    $br=0;
            }   
            $groupname=$redis->hGet('group:'.$group.':info', 'groupname');
                    $checked='checked';

            print <<<EOT
                        <input type="checkbox" $checked value=$group name=rwgroups[]>$groupname$group |
EOT;
            $br++;
}

print <<<EOT
<br />
<input type="submit" name=edit value="提交变更">
</form>
EOT;

//编辑模式时显示成员调整
$trColor="#c0c0c0";
if(!empty($project)){
	print <<<EOT
<hr>
增加授权:
<form action="/models/projectgrant.php" method=POST>
<input type="hidden" name=project value=$project>
<input type="submit" name=projro value="只读授权">
<input type="submit" name=projrw value="读写授权">
<br />
用户列表<br />
<table border="0" cellspacing="1" bgcolor="#000000">
<tr bgcolor=$trColor>\n
EOT;
	//按英文姓名排序
	$freeUsers=$redis->sDiff('user:all','project:'.$project.':ro','project:'.$project.':rw');
	sort($freeUsers);

	//换行变量
	$br=0;
	foreach($freeUsers as $user){
            if($br==5){
                    echo "</tr>\n";
                    $br=0;
					if($trColor=="#DDDDDD"){
                            $trColor="#c0c0c0";
                    }else{
                            $trColor="#DDDDDD";
                    }   
                    echo "\n<tr bgcolor=$trColor>\n";

            }   

            $username=$redis->hGet('user:'.$user.':info', 'username');

            print <<<EOT
                       <td> <input type="checkbox" value=$user name=users[]>$username$user </td>\n
EOT;
            $br++;
	}

	print <<<EOT
</tr>
</table>
EOT;

	print <<<EOT
组列表<br />
<table border="0" cellspacing="1" bgcolor="#000000">
<tr bgcolor=$trColor>\n
EOT;
	//按英文姓名排序
	$freeGroups=$redis->sDiff('group:all','project:'.$project.':gro','project:'.$project.':grw');
	sort($freeGroups);

	//换行变量
	$br=0;
	foreach($freeGroups as $group){
            if($br==4){
                    echo "</tr>\n";
                    $br=0;
					if($trColor=="#DDDDDD"){
                            $trColor="#c0c0c0";
                    }else{
                            $trColor="#DDDDDD";
                    }   
                    echo "\n<tr bgcolor=$trColor>\n";

            }   

            $groupname=$redis->hGet('group:'.$group.':info', 'groupname');

            print <<<EOT
                       <td> <input type="checkbox" value=$group name=groups[]>$groupname$group </td>\n
EOT;
            $br++;
	}

	print <<<EOT
</tr>
</table>
EOT;


	echo '</form>';
}
?>
