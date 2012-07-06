<?php
//Flydragon<ipv6china@comsenz.com
if(!$standalone){
		include_once("../view/header.php");
		include_once("../common.php");
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

$projectpath=$_POST['projectpath'];
$projectname=$_POST['projectname'];
$department=$_POST['department'];
$owner=$_POST['owner'];
$contact=$_POST['contact'];
$project=md5($projectpath);

//判断权限
$ret=checkProjectPerm($projectpath,$authUser,$redis);
if(($authUser != $superAdmin)&&!$ret){
	die('没有修改此组的权限!');
}   

$redis->hMset('project:'.$project.':info', 
				array('projectname' => $projectname,
						'owner' => $owner,
						'projectpath' => $projectpath,
						'department' => $department,
						'contact' => $contact));

$redis->sAdd('user:'.$owner.':ownproj',$projectpath);

$redis->sAdd('project:all', $projectpath);
$redis->hSet('md5index', $project, $projectpath);

//最新项目列表
$redis->lPush('project:last',$projectname.':'.$projectpath);
//############################### 更新只读成员
$oldro = $new = array();
$oldro = $redis->sMembers('project:'.$project.':ro');
$new = $_POST['rousers'];
if(empty($new)) $redis->Del('project:'.$project.':ro');
//需要删除的
$pops=array_diff($oldro,$new);
foreach($pops as $pop){
	$redis->sRem('project:'.$project.':ro', $pop);
	$redis->sRem('user:'.$pop.':inproj', $projectpath);
}
//需要添加的(clone)
$adds=array_diff($new, $oldro);
foreach($adds as $add){
	$redis->sAdd('project:'.$project.':ro', $add);
	$redis->sAdd('user:'.$add.':inproj', $projectpath);
}
//############################### 更新只读组成员end

//############################### 更新读写成员
$oldrw = $new = array();
$oldrw = $redis->sMembers('project:'.$project.':rw');
$new = $_POST['rwusers'];
if(empty($new)) $redis->Del('project:'.$project.':rw');
//需要删除的
$pops=array_diff($oldrw,$new);
foreach($pops as $pop){
	$redis->sRem('project:'.$project.':rw', $pop);
	$redis->sRem('user:'.$pop.':inproj', $projectpath);
}
//需要添加的(clone)
$adds=array_diff($new, $oldrw);
foreach($adds as $add){
	$redis->sAdd('project:'.$project.':rw', $add);
	$redis->sAdd('user:'.$add.':inproj', $projectpath);
}
//############################### 更新读写组成员end

//############################### 更新只读组
$oldgro = $new = array();
$oldgro = $redis->sMembers('project:'.$project.':gro');
$new = $_POST['rogroups'];
if(empty($new)) $redis->Del('project:'.$project.':gro');
//需要删除的
$pops=array_diff($oldgro,$new);
foreach($pops as $pop)
	$redis->sRem('project:'.$project.':gro', $pop);
//需要添加的(clone)
$adds=array_diff($new, $oldgro);
foreach($adds as $add)
	$redis->sAdd('project:'.$project.':gro', $add);
//############################### 更新只读组end


//############################### 更新读写组
$oldgrw = $new = array();
$oldgrw = $redis->sMembers('project:'.$project.':grw');
$new = $_POST['rwgroups'];
if(empty($new)) $redis->Del('project:'.$project.':grw');
//需要删除的
$pops=array_diff($oldgrw,$new);
foreach($pops as $pop)
	$redis->sRem('project:'.$project.':grw', $pop);
//需要添加的(clone)
$adds=array_diff($new, $oldgrw);
foreach($adds as $add)
	$redis->sAdd('project:'.$project.':grw', $add);
//############################### 更新读写组end

echo <<<EOF
    <script language="JavaScript">
    window.location="/project.php?mod=edit&project=$project"
	alert('修改已经提交。');
    </script>
EOF;

}else{
		$project=@$_GET['project'];
		$projectpath=$redis->hGet('md5index', $project);
		if($mod == 'cloneadd'){
				$projectpath=$projectpath.$project;
		}
		$projectname=$redis->hGet('project:'.$project.':info', 'projectname');
		$department=$redis->hGet('project:'.$project.':info', 'department');
		$owner=$redis->hGet('project:'.$project.':info', 'owner');
		$contact=$redis->hGet('project:'.$project.':info', 'contact');
}
