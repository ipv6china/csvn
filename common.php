<?php
//Flydragon<ipv6china@comsenz.com
//debug mode
ini_set('display_errors', 1); 

include_once('config.inc.php');
//初始化redis长连接
$redis = new Redis();
$ret=$redis->pconnect($redisHost,$redisPort,$redisTimeout);
if(!$ret)die('Can\'t connect redis server,die!');
$redis->auth($redisPass);
$redis->select($redisDbName);

//auth
if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_USER'])){
		$authed=false;
}else{
		$authUser=$_SERVER['PHP_AUTH_USER'];
		$authPW=$_SERVER['PHP_AUTH_PW'];
		@$sysPass=$redis->hGet('user:'.$authUser.':info','authpw');
		if($redis->exists('user:'.$authUser.':info')&&testCrypt($sysPass,$authUser,$authPW)){
				$authed=true;
		}else{
				$authed=false;

		}
}
if($authed == false){
	header('WWW-Authenticate: Basic realm="SVN control panel auth"');
	header('HTTP/1.0 401 Unauthorized');
	exit;
}
function printClose(){
echo <<<EOF
    <script language="JavaScript">
        self.resizeTo(800,600);
        alert('修改已经提交，窗口将自动关闭。'); 
        window.close();
    </script>
EOF;
}

function checkProjectPerm($projectPath,$user,$redisRes){
		$authUser=$user;
		$redis=$redisRes;
        if(!$redis->exists('user:'.$authUser.':ownproj')){
                return false;
        }else{
                $ownProjs = $redis->sMembers('user:'.$authUser.':ownproj');
        }   
    
        foreach($ownProjs as $ownProj){
			$pos=strpos($projectPath, $ownProj);
            if($pos !== false){
                    return true;
			}
        }   
        return false;
}

//////////////////////////备份数据库////////////////////////
function backupDatabase($redis, $buildmsg=''){
	$backupFile='data/backup/'.time().'.db';
	$fp=fopen($backupFile, 'wb+');
	if(!$fp) die('Can\'t open dotfile:$dotFile，die!');
	$content='';   

	//用户
	$allUsers=$redis->sMembers('user:all');
	sort($allUsers);

	foreach($allUsers as $user){
		$userInfo = array('username' => '',
				'password' => '',
				'department' => '',
				'contact' => '',
				'authpw' => '');

		$_userInfo=$redis->hGetAll('user:'.$user.':info');
		$userInfo = array_merge($userInfo, $_userInfo);

		$content.= 'UU|'. 
			$user.'|'.
			$userInfo['username'].'|'.
			$userInfo['password'].'|'.
			$userInfo['department'].'|'.
			$userInfo['contact'].'|'.
			$userInfo['authpw']."\n";
	}

	//用户组
	$allGroups=$redis->sMembers('group:all');
	sort($allGroups);
	//组列表
	foreach($allGroups as $group){
		$groupInfo=array('groupname' => '',
				'department' => '',
				'owner' => '',
				'contact' => '',
				'members' => '');
		$_groupInfo=$redis->hGetAll('group:'.$group.':info');
		$groupInfo=array_merge($groupInfo, $_groupInfo);
	
		$groupInfo['members']='{'.implode(',',$redis->sMembers('group:'.$group.':members')).'}';

		$content.='GG|'.
		 	$group.'|'.
		 	$groupInfo['groupname'].'|'.
		 	$groupInfo['department'].'|'.
		 	$groupInfo['owner'].'|'.
		 	$groupInfo['contact'].'|'.
		 	$groupInfo['members']."\n";
	}

	//项目
	$allProjects=$redis->sMembers('project:all');
	sort($allProjects);
	foreach($allProjects as $project){
			$projectId=md5($project);
			$projectInfo=array('projectId' => '',
							'projectpath' => '',
							'department' => '',
							'projectname' => '',
							'owner'	=> '',
							'contact' =>'',
							'grws' => '',
							'gros' => '',
							'rws' => '',
							'ros' => '');
			$_projectInfo=$redis->hGetAll('project:'.$projectId.':info');
			$projectInfo=array_merge($projectInfo, $_projectInfo);
			$projectInfo['projectId']=$projectId;
			$projectInfo['grws']='{'.implode(',',$redis->sMembers('project:'.$projectId.':grw')).'}';
			$projectInfo['gros']='{'.implode(',',$redis->sMembers('project:'.$projectId.':gro')).'}';
			$projectInfo['rws']='{'.implode(',',$redis->sMembers('project:'.$projectId.':rw')).'}';
			$projectInfo['ros']='{'.implode(',',$redis->sMembers('project:'.$projectId.':ro')).'}';

			$content.='PP|'.
			$projectInfo['projectId'].'|'.
			$projectInfo['projectpath'].'|'.
			$projectInfo['projectname'].'|'.
			$projectInfo['department'].'|'.
			$projectInfo['owner'].'|'.
			$projectInfo['contact'].'|'.
			$projectInfo['grws'].'|'.
			$projectInfo['gros'].'|'.
			$projectInfo['rws'].'|'.
			$projectInfo['ros']."\n";
	}



	//写入文件
	if(fwrite($fp, $content) === FALSE){
        	die("无法写入文件$dotFile");
	}
	fclose($fp);
	//写build日志
	$redis->set(md5($backupFile).'.msg', $buildmsg);
	
	if(DEBUG){
    	print <<<EOT
    <pre>
$content
    </pre>
EOT;
	}else{
		print <<<EOF
    <script language="JavaScript">
        alert("已将此版本备份为:
EOF;
echo $backupFile;
print <<<EOF
"); 
        window.close();
    </script>
EOF;
}
}

//////////////////////////恢复数据库////////////////////////
function restoreDatabase($ver, $redis){
$redis->flushdb();

$fp= fopen('data/backup/'.$ver.'.db', 'r');
while(!feof($fp)){
$_line=chop(fgets($fp));
if(strlen($_line)<6)continue;
	$_row=explode('|', $_line);
	if('UU' == $_row[0]){
		$user=$_row[1];
		$username=$_row[2];
		$password=$_row[3];
		$department=$_row[4];
		$contact=$_row[5];
		$authpw=$_row[6];
		
		$redis->sAdd('user:all', $user);
		$redis->hMset('user:'.$user.':info', array('username' => $username,
					'password' => $password,
					'department' => $department,
					'contact' => $contact,
					'authpw' => $authpw));
	}elseif('GG' == $_row[0]){
		$group=$_row[1];
		$groupname=$_row[2];
		$department=$_row[3];
		$owner=$_row[4];
		$contact=$_row[5];
		$members=explode(',',substr($_row[6],1,-1));
		$redis->sAdd('group:all', $group);
		$redis->hMset('group:'.$group.':info',array('groupname' => $groupname,
					'department' => $department,
					'owner'	=> $owner,
					'contact' => $contact));
		$redis->sAdd('user:'.$owner.':owngrp',$owner);
		foreach($members as $user){
			if($user != '')
				$redis->sAdd('group:'.$group.':members',$user);
		}

	}elseif('PP' == $_row[0]){
		$projectId=$_row[1];
		$projectpath=$_row[2];
		$projectname=$_row[3];
		$department=$_row[4];
		$owner=$_row[5];
		$contact=$_row[6];
		$grws=explode(',',substr($_row[7], 1, -1));
		$gros=explode(',',substr($_row[8], 1, -1));
		$rws=explode(',',substr($_row[9], 1, -1));
		$ros=explode(',',substr($_row[10], 1, -1));
		$redis->sAdd('project:all', $projectpath);
		$redis->hSet('md5index', $projectId, $projectpath);
		$redis->sAdd('user:'.$owner.':ownproj', $owner);
		$redis->hMset('project:'.$projectId.':info', array('projectname' => $projectname,
					'projectpath' => $projectpath,
					'owner' => $owner,
					'department' => $department,
					'contact' => $contact));
		foreach($grws as $user){
			if($user != '')
				$redis->sAdd('project:'.$projectId.':grw', $user);
		}
		foreach($gros as $user){
			if($user != '')
				$redis->sAdd('project:'.$projectId.':gro', $user);
		}
		foreach($rws as $user){
			if($user != '')
				$redis->sAdd('project:'.$projectId.':rw', $user);
		}
		foreach($ros as $user){
			if($user != '')
				$redis->sAdd('project:'.$projectId.':ro', $user);
		}
	
	}else{
		continue;
	}
 }

fclose($fp);
print <<<EOF
    <script language="JavaScript">
        self.resizeTo(800,600);
        alert('已经恢复到新版本。'); 
        window.close();
    </script>
EOF;
}

///////////////加密函数///////////////////
function myCrypt( $pass )
{
  $salt = ""; 
  mt_srand((double)microtime()*1000000);
  for ($i=0; $i<CRYPT_SALT_LENGTH; $i++)
    $salt .= substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./", mt_rand() & 63, 1);   return crypt($pass, $salt);
}
///////// 解密函数
function testCrypt( $pass_in_db, $user, $pass )
{
  $crypted = $pass_in_db;

    return crypt( $pass, substr($crypted,0,CRYPT_SALT_LENGTH) ) == $crypted;
}
