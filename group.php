<?php
include_once("common.php");
//"MVC"模式
$standalone='no';

if($_SERVER['REQUEST_METHOD']=='GET'){
		$mod=(!empty($_GET['mod']))?$_GET['mod']:'list';
	    $allUsers = $redis->sMembers('user:all');

		if($mod == 'list'){
				$pageTitle="所有组列表";
				$allGroups = $redis->sMembers('group:all');
				$groupNum = $redis->sCard('group:all');
				include_once("view/grouplist.php");
		}
		if($mod == 'add'){
				$pageTitle="添加新组";
				$idType="text";
				include_once("view/groupedit.php");
		}
		if($mod == 'del'){
				$pageTitle="删除组";
				include_once("models/groupdel.php");
		}
		if($mod == 'edit') {
				$pageTitle="编辑组";
				$idType="hidden";
				include_once("view/groupedit.php");

		}
		if($mod == 'showgrant') {
				$pageTitle="查看组权限";
				include_once("view/showgroupgrant.php");
		}
}
