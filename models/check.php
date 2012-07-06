<?php
include_once("../common.php");

$mod=$_GET['mod'];

if($mod == 'user'){
	$u=$_GET['u'];
}elseif($mod == 'group'){
	$g=$_GET['g'];	
}else{$mod == 'project'){
	$p=$_GET['p'];

}
