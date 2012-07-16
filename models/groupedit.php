<?php
if(!$standalone){
		include_once("../view/header.php");
		include_once("../common.php");
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$group=$_POST['group'];
	if($_POST['modify']){
		//判断权限
	    $groupOwner=$redis->hGet('group:'.$group.':info', 'owner');
		if(($authUser != $groupOwner)&&($authUser != $superAdmin)){
				die('没有修改此组的权限!');
		}


		$owner=$_POST['owner'];
		$groupname=$_POST['groupname'];
		$department=$_POST['department'];
		$contact=$_POST['contact'];


		$redis->hMset('group:'.$group.':info', 
				array('groupname' => $groupname,
					'owner' => $owner,
					'department' => $department,
					'contact' => $contact));

		$redis->sAdd('group:all', $group);
		//最新组列表
		$redis->lPush('group:last',$group);
		
		$redis->sAdd('user:'.$owner.':owngrp',$group);

		//############################### 更新组成员
		//利用数组的差集取出需要添加和删除的成员,这块因为没有事务支持，可能会有些问题
		$old=$new=array();
		$old = $redis->sMembers('group:'.$group.':members');
		$new = $_POST['users'];
		if(empty($new))$redis->Del('group:'.$group.':members');
		//需要删除的
		$pops=array_diff($old,$new);
		foreach($pops as $pop){
			//从组成员移除
			$redis->sRem('group:'.$group.':members',$pop);
			//从用户参加的组集合中移除组
			$redis->sRem('user:'.$pop.':ingroup', $group);
		}
		//需要添加的
		$adds=array_diff($new,$old);
		foreach($adds as $add){
			//添加用户进组
			$redis->sAdd('group:'.$group.':members',$add);
			//添加组名到用户的ingroup集合
			$redis->sAdd('user:'.$add.':ingroup', $group);
		}
	}elseif($_POST['addmember']){
		//判断权限
	    $groupOwner=$redis->hGet('group:'.$group.':info', 'owner');
		if(($authUser != $groupOwner)&&($authUser != $superAdmin)){
				die('没有修改此组的权限!');
		}

		$addusers=$_POST['addusers'];
		foreach($addusers as $user){
				//添加进组
		$redis->sAdd('group:'.$group.':members',$user);
			//添加用户的ingroup集合
			$redis->sAdd('user:'.$user.':ingroup', $group);
		}
			
		$redis->sAdd('group:all', $group);
}
//############################### 更新组成员end
echo <<<EOF
    <script language="JavaScript">
    window.location="/group.php?mod=edit&group=$group"
	alert('修改已经提交。');
    </script>
EOF;

}else{
$group=@$_GET['group'];
$groupname=$redis->hGet('group:'.$group.':info', 'groupname');
$owner=$redis->hGet('group:'.$group.':info', 'owner');
$department=$redis->hGet('group:'.$group.':info', 'department');
$contact=$redis->hGet('group:'.$group.':info', 'contact');

}
