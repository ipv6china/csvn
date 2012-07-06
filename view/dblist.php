<?php
//Flydragon<ipv6china@comsenz.com
include_once("view/header.php");
print <<<EOT
<h3>版本管理</h3>
<a href=dbManager.php target=_blank>生成新备份</a> 
<br />
<table border="0" cellspacing="1" bgcolor="#000000">
<tr bgcolor=#646464>
	<td> 文件名</td>
	<td> 生成时间 </td>
	<td> 发布注释 </td>
	<td> 操作 </td>

</tr>
EOT;
//读取所有备份文件
$handle=opendir($dataBakDir);
$i=0;
if(!$handle) die('Can\'t open data/backup');
while($file=readdir($handle)){
	if($file != '.' && $file != '..' && $file != 'index.htm')
		$allFiles[$i]=$file;	
		$i++;
}
closedir($handle);

$tr=0;
if(isset($allFiles)){
	sort($allFiles);
	foreach($allFiles as $filename){
	
			//隔行换颜色
			if($tr%2==0)
				$trColor="#969696";
			else
				$trColor="#CCCCCC";
		
		$time=explode('.',$filename);
		$time=$time[0];
		date_default_timezone_set('Asia/Shanghai');
		$ctime=date(DATE_RFC822, $time);
		$buildmsg=$redis->Get(md5($dataBakDir.'/'.$filename).'.msg');
		print <<<EOT
<tr bgcolor=$trColor>
	<td> $filename</td>
	<td> $ctime</td>
	<td> $buildmsg</td>
	<td>
		<a href=database.php?mod=view&ver=$time target=_blank> 查看</a> |
		<a href=database.php?mod=change&ver=$time target=_blank> 切换到此版本</a> |
		<a href=database.php?mod=del&ver=$time target=_blank> 删除</a> 
	</td>
</tr>
EOT;
		$tr++;

	}
}


print <<<EOT
</table>
<hr>
<h3>从本地导入</h3>
<form enctype="multipart/form-data" action="models/import.php" method="POST">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
    <!-- Name of input element determines name in $_FILES array -->
    备份文件: <input name="userfile" type="file" />
    <input type="submit" value="导入" />
</form>
EOT;
