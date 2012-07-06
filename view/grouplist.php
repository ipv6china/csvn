<?php
//Flydragon<ipv6china@comsenz.com
include_once("view/header.php");

print <<<EOT
<a href=group.php?mod=add target=_blank>添加新组</a>  共有组${groupNum}个
<br />
<table border="0" cellspacing="1" bgcolor="#000000">
<tr bgcolor=#646464>
	<td>权限</td>
	<td> 组ID </td>
	<td> 用途 </td>
	<td> 部门 </td>
	<td> 联系方式 </td>
	<td> 组长 </td>
	<td> 成员数量 </td>
	<td> 操作 </td>

</tr>
EOT;
$tr=0;
sort($allGroups);
foreach($allGroups as $group){

		$groupname=$redis->hGet('group:'.$group.':info','groupname');
		$department=$redis->hGet('group:'.$group.':info','department');
		$contact=$redis->hGet('group:'.$group.':info','contact');
		$owner=$redis->hGet('group:'.$group.':info','owner');
		$memberCount=$redis->sCard('group:'.$group.':members');
        //隔行换颜色
        if($tr%2==0)
            $trColor="#969696";
        else
            $trColor="#CCCCCC";

		print <<<EOT
<tr bgcolor=$trColor>
	<td> <a href=group.php?mod=showgrant&group=$group target=_blank>view</a> </td>
	<td> $group </td>
	<td> $groupname </td>
	<td> $department </td>
	<td> $contact </td>
	<td> $owner </td>
	<td> $memberCount </td>
	<td>
		<a href=group.php?mod=edit&group=$group target=_blank><img src=images/edit.gif>编辑 </a> |
		<a href=group.php?mod=del&group=$group target=_blank><img src=images/delete.gif>删除 </a> 
	</td>
</tr>
EOT;
$tr++;

}

print <<<EOT
</table>
EOT;
