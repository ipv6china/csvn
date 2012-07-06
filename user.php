<?php
//Flydragon<ipv6china@comsenz.com
include_once('common.php');
$standalone='no';

if($_SERVER['REQUEST_METHOD']=='GET'){
		$mod=(!empty($_GET['mod']))?$_GET['mod']:'list';

		if($mod == 'list'){
				$pageTitle='所有用户列表';
				$userNum=$redis->sCard('user:all');
				$allUsers = $redis->sMembers('user:all');
				include_once('view/userlist.php');
		}
		if($mod == 'add'){
				$pageTitle='添加新用户';
				$idType='text';
				include_once('view/useredit.php');
		}
		if($mod == 'del'){
				$pageTitle='删除用户';
				include_once('models/userdel.php');
		}
		if($mod == 'edit') {
				$pageTitle='编辑用户';
				$idType='hidden';
				include_once('view/useredit.php');

		}
		if($mod == 'showgrant'){
				$pageTitle='显示权限';
				include_once('view/showusergrant.php');
		}

}

