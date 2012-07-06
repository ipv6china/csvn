<?php
#debug mode
ini_set('display_errors', 1); 

include_once('config.inc.php');
//初始化redis长连接
$redis = new Redis();
$redis->pconnect($redisHost,$redisPort,$redisTimeout);
$redis->auth('redispass');
$redis->select(3);


