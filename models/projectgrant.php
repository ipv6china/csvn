<?php
//Flydragon<ipv6china@comsenz.com
if(!$standalone){
		include_once("../view/header.php");
		include_once("../common.php");
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$project=$_POST['project'];

        //判断权限
        $ownerOld=$redis->hGet('project:'.$project.':info', 'owner');
        if(($authUser != $ownerOld)&&($authUser != $superAdmin)){
                die('没有修改此组的权限!');
        }   


	$users=$_POST['users'];
	$groups=$_POST['groups'];

	//增加读写授权
	if($_POST['projrw']){
		foreach($users as $user)
			$redis->sAdd('project:'.$project.':rw', $user);
		foreach($groups as $group)
			$redis->sAdd('project:'.$project.':grw', $group);
	}
	//增加只读授权
	if($_POST['projro']){
		foreach($users as $user)
			$redis->sAdd('project:'.$project.':ro', $user);
		foreach($groups as $group)
			$redis->sAdd('project:'.$project.':gro', $group);
	}
	
	echo <<<EOF
    <script language="JavaScript">
		window.location="/project.php?mod=edit&project=$project"
    </script>
EOF;

}else{
	echo '不支持的方法';
}
