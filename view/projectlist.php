<?php
//Flydragon<ipv6china@comsenz.com
include_once("view/header.php");

print <<<EOT
<a href=project.php?mod=add target=_blank>添加新项目</a>  共有项目节点${projectNum}个
<br />
<table border="1" cellspacing="1" bgcolor="#000000">
<tr bgcolor=#646464>
	<td>权限</td>
	<td> 项目路径 </td>
	<td> 项目名 </td>
	<td> 部门 </td>
	<td> 联系方式 </td>
	<td> 项目组长 </td>
	<td> 操作 </td>

</tr>
EOT;

$tr=0;
sort($allProjects);
foreach($allProjects as $project){
		$projectpath=$project;
		//如果没有权限就不显示
		if($authUser != $superAdmin){
			if(!checkProjectPerm($projectpath,$authUser,$redis))continue;
		}

		$project=md5($project);
		$projectname=$redis->hGet('project:'.$project.':info','projectname');
		$department=$redis->hGet('project:'.$project.':info','department');
		$contact=$redis->hGet('project:'.$project.':info','contact');
		$owner=$redis->hGet('project:'.$project.':info','owner');
        //隔行换颜色
        if($tr%2==0)
            $trColor="#969696";
        else
            $trColor="#CCCCCC";

   		$count = substr_count($projectpath, '/');
    	if($count == 1)
				$rootColor='red';
		else
				$rootColor='black';

		print <<<EOT
<tr bgcolor=$trColor>
	<td> <a href=project.php?mod=showgrant&project=$project target=_blank>View</a></td>
	<td><font color=$rootColor> $projectpath </font></td>
	<td> $projectname </td>
	<td> $department </td>
	<td> $contact </td>
	<td> $owner </td>
	<td>
		<a href=project.php?mod=edit&project=$project target=_blank> <img src=images/edit.gif>编辑 </a> |
		<a href=project.php?mod=cloneadd&project=$project target=_blank> <img src=images/clone.gif>克隆 </a> |
		<a href=project.php?mod=del&project=$project target=_blank> <img src=images/delete.gif>删除 </a> 
	</td>
</tr>
EOT;
$tr++;
}

print <<<EOT
</table>
EOT;
