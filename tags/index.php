<?php
include_once('common.php');
//"MVC"模式
$standalone='no';

if($_SERVER['REQUEST_METHOD']=='GET'){
		$mod=(!empty($_GET['mod']))?$_GET['mod']:'list';
		$admin=(!empty($_GET['admin']))?$_GET['admin']:'';

		if($mod == 'list'){
				$pageTitle='所有收藏列表';
				include_once('view/favlist.php');
		}
		if($mod == 'add'){
				$pageTitle='添加新收藏';
				$idType='text';
				include_once('view/favedit.php');
		}
		if($mod == 'del'){
				$pageTitle='删除收藏';
				include_once('models/favdel.php');
		}
		if($mod == 'edit') {
				$pageTitle='编辑收藏';
				$idType='hidden';
				include_once('view/favedit.php');

		}
}
