<?php
#debug mode
ini_set('display_errors', 1); 

if($argc !=2){ 
    print <<<EOT
Usage:
  $argv[0] <password>

EOT;
}


include_once('../config.inc.php');
//初始化redis长连接
$redis = new Redis();
$ret=$redis->pconnect($redisHost,$redisPort,$redisTimeout);
if(!$ret)die('Can\'t connect redis server,die!');
$redis->auth($redisPass);
$redis->select($redisDbName);

if(!$redis->exists('user:'.$superAdmin.':info')) die("没有此用户[no user $superAdmin !]");
$redis->hSet('user:'.$superAdmin.':info', 'authpw',myCrypt($argv[1]));
echo "Password changed!\n";

function myCrypt( $pass )
{
  $salt = ""; 
  mt_srand((double)microtime()*1000000);
  for ($i=0; $i<CRYPT_SALT_LENGTH; $i++)
    $salt .= substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./", mt_rand() & 63, 1);   return crypt($pass, $salt); 
}

