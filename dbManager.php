<?php
//Flydragon<ipv6china@comsenz.com
error_reporting (E_ALL);
define('DEBUG', false);
$pageTitle="备份数据库";
include_once("common.php");
include_once("view/header.php");

backupDatabase($redis);
