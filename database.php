<?php
//Flydragon<ipv6china@comsenz.com
$pageTitle="数据库管理";
include_once('common.php');
include_once("view/header.php");
$standalone='no';
if($authUser != $superAdmin) die("没有权限");

if($_SERVER['REQUEST_METHOD']=='GET'){
		$mod=(!empty($_GET['mod']))?$_GET['mod']:'list';

		//////////////
		if($mod == 'list'){
				$pageTitle='备份列表';
				include_once('view/dblist.php');
		}
		//////////////
		if($mod == 'del'){
				$ver=$_GET['ver'];
				if(unlink('data/backup/'.$ver.'.db')){
						echo <<<EOF
						<script language="JavaScript">
						    self.resizeTo(800,600);
							alert('修改已经提交，窗口将自动关闭。'); 
							window.close();
						</script>
EOF;

				}
		}
		//////////////
		if($mod == 'change'){
				$ver=$_GET['ver'];
				restoreDatabase($ver, $redis);

		}
		//////////////
		if($mod == 'view'){
				$ver=$_GET['ver'];
				$content=file_get_contents($dataBakDir.'/'.$ver.'.db');
				print <<<EOT
			<pre>
				$content
			</pre>
EOT;

		}

		

}

