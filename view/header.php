<?php
include_once("common.php");
print <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="Content-Language" content="zh-cn" />
<title>$pageTitle</title>
</head>
<body>
<h1>SVN管理系统</h1>
<a href=/>首页</a> |
<a href=user.php>用户管理</a> | 
<a href=group.php>组管理</a> |
<a href=project.php>项目管理</a> |
<a href=build.php>发布配置</a> 
EOT;
if($authUser == $superAdmin){
	print <<<EOT
| <a href=database.php>数据管理</a> |
<a href=graph.php>全局视图</a> |
<a href=tools.php>管理者工具</a>
EOT;
}

print <<<EOT
&nbsp;&nbsp;&nbsp;&nbsp;
欢迎你$authUser
<hr>
EOT;

