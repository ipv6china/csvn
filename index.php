<?php
//Flydragon<ipv6china@comsenz.com
$pageTitle="首页";
include_once("view/header.php");
include_once("common.php");
include_once("models/lastlist.php");
$userNum=$redis->sCard('user:all');
$groupNum=$redis->sCard('group:all');
$projectNum=$redis->sCard('project:all');

$lastusers=$redis->lRange('user:last', 0, -1);
$lastgroups=$redis->lRange('group:last', 0, -1);
$lastprojects=$redis->lRange('project:last', 0, -1);

?>
<h4>统计信息</h4> 
共有 用户<?php echo $userNum;?>位，组<?php echo $groupNum;?>个, 独立授权的svn叶子节点<?php echo $projectNum;?>个。<br /><br />

<h4>最进更新的项目节点:</h4>
<?php echo implode("\n<br />",$lastprojects);?>

