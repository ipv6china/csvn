<?php
//Flydragon<ipv6china@comsenz.com
include_once("common.php");
$standalone='no';

if($_SERVER['REQUEST_METHOD']=='GET'){
		$mod=(!empty($_GET['mod']))?$_GET['mod']:'list';

		if($mod == 'list'){
				$pageTitle="所有项目列表";
				$allProjects = $redis->sMembers('project:all');
				$projectNum = $redis->sCard('project:all');
				include_once("view/projectlist.php");
		}
		if($mod == 'add'){
				$pageTitle="添加新项目";
				include_once("view/projectedit.php");
		}
		if($mod == 'del'){
				$pageTitle="删除项目";
				include_once("models/projectdel.php");
		}
		if($mod == 'edit') {
				$pageTitle="编辑项目";
				include_once("view/projectedit.php");
		}
		if($mod == 'cloneadd') {
				$pageTitle="克隆编辑项目";
				include_once("view/projectedit.php");
		}
		if($mod == 'showgrant') {
				$pageTitle="显示权限图";
				include_once("view/showprojgrant.php");
		}
}
