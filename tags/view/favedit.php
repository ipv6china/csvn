<?php
include_once("view/header.php");
include_once("models/favedit.php");

print <<<EOT
    <script language="JavaScript">
	 	self.resizeTo(800,600);
	</script>
<h1>$pageTitle</h1>
<form action="/tags/models/favedit.php" method=POST>
<table border=1 >
<tr><td>网站名称</td><td><input type="text" name="sitename" value="$sitename"></td></tr>
<tr><td>网站地址</td><td><input type="text" name="url" value="$url"></td></tr>
<tr><td>标签列表</td><td><input type="text" name="tags" value="$tags">用空格分开</td></tr>
<tr><td>简介</td><td><input type="text" name="desc" value="$desc"></td></tr>
</table><br />
<input type="submit" >
</form>
EOT;
