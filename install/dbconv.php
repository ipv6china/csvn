#!/usr/bin/php
<?php
if($argc !=3 && in_array($argv[1],array('-pass', '-auth'))){
	print <<<EOT
Usage:
  $argv[0] <-pass|-auth> filename 

EOT;

} else{
	//转换账户
	if($argv[1]=='-pass'){
			$fd=fopen($argv[2],'rb');
			if($fd){
				$content='';
				$line='';
				//循环读
				while(!feof($fd)){
					$_line=fgets($fd,4096);
					//去掉结尾换行
					$_line=chop($_line);

					//去掉注释行
					//去掉短行
					if(strlen($_line)<12) continue;

					//分割用户名密码
					$line=explode(':', $_line);
					$user=$line[0];
					$password=$line[1];

					$content='UU|'.
							$user.'|'.
							'|'.
							$password.'|'.
							'||'."\n";

					echo $content;
				}
			}

			fclose($fd);
	}

	//转换组和项目授权
	if($argv[1]=='-auth'){
		$ini_array = parse_ini_file($argv[2], true);
		foreach($ini_array as $k => $v){
				if($k=='groups'){
						foreach($v as $group => $members){
								$members='{'.$members.'}';
								$group=str_replace(' ', '_', $group);
								$content='GG'.'|'.
										$group.'|'.
										'||||'.
										$members."\n";
								echo $content;

						}
				}else{
						// [DiscuzMobile:/]  [DiscuzMobile:/IOS] 
						//处理路径格式转换
						$count = substr_count($k, '/');
						$last=substr($k,-1,1);
						if($count > 1 && $last == '/') $project=chop($k);
						if($count=1 && $last == '/'){
								$projectpath=preg_replace("/(.*):\/$/i","/\$1",$k);
						}else{
								$projectpath=preg_replace("/(.*):(.*)$/i","/\$1\$2",$k);
						}
							$projectId=md5($projectpath);
						//处理权限
							$rs=$rws=$grs=$grws=array();
							foreach($v as $name => $priv){
								if('*'==$name[0]) continue;
								//(strpos($name, '*')!==FALSE) && continue;
								if('@'==$name[0]){
										$name=substr($name,1);
										$name=str_replace(' ', '_', $name);
										'r' == $priv?$grs[]=$name:$grws[]=$name;
								}else{
										'r' == $priv?$rs[]=$name:$rws[]=$name;
								}
							}
						//输出
						$rs='{'.implode(',',$rs).'}';
						$rws='{'.implode(',',$rws).'}';
						$grs='{'.implode(',',$grs).'}';
						$grws='{'.implode(',',$grws).'}';

						$content='PP|'.
								$projectId.'|'.
								$projectpath.'|'.
								'||||'.
								$grws.'|'.
								$grs.'|'.
								$rws.'|'.
								$rs.'|'."\n";
						echo $content;

				}//$k=='groups' else end
		}//$ini_array end
	}//转换组和项目授权end
}//global end

?>
