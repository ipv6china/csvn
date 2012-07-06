<?php
//Flydragon<ipv6china@comsenz.com
if(!$standalone){
include_once("../view/header.php");
include_once("../common.php");
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
$user=$_POST['user'];
if($authUser != $superAdmin)
	die("没有权限修改");

//修改SVN密码
if(isset($_POST['chpw'])){
	$password=myCrypt($_POST['password']);
}else{
	$password=$redis->hGet('user:'.$user.':info', 'password');
}
//修改管理密码
if(isset($_POST['chadminpw'])){
	$authpw=myCrypt($_POST['authpw']);
}else{
	$authpw=$redis->hGet('user:'.$user.':info', 'authpw');
}

$username=$_POST['username'];
$department=$_POST['department'];
$contact=$_POST['contact'];
$redis->hMset('user:'.$user.':info',
				array('username' => $username,
						'password' => $password,
						'department' => $department,
						'contact' => $contact,
						'authpw' => $authpw));
//解除管理权限
if(isset($_POST['deladmin'])){
	$redis->hDel('user:'.$user.':info', 'authpw');
}

//所有用户集合
$redis->sAdd('user:all', $user);
//最新用户列表
$redis->lPush('user:last',$user);

echo <<<EOF
    <script language="JavaScript">
    window.location="/user.php?mod=edit&user=$user"
	alert('修改已经提交。');
//
        self.resizeTo(800,600);
        window.close();

    </script>
EOF;

}else{

$user=@$_GET['user'];
$username=$redis->hGet('user:'.$user.':info', 'username');
$password=$redis->hGet('user:'.$user.':info', 'password');
$department=$redis->hGet('user:'.$user.':info', 'department');
$contact=$redis->hGet('user:'.$user.':info', 'contact');
$authpw=$redis->hGet('user:'.$user.':info', 'authpw');

}
