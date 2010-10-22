<?
$memory_last_line = exec('free |grep Mem',$memory);
$full = str_replace(' ','.',$memory_last_line);
$full = str_replace('Mem:.......','',$full);
$full = str_replace('....','X',$full);
$full = str_replace('.','',$full);
$parts = explode('X',$full);
$kb = $parts['2'];
$mb = $kb / 1000; 
$final = number_format($mb,0);
$final = str_replace(',','',$final);
if ($final < 100){
	error_log("[".date('r')."] [Server is running with ".$final."MB Free, clearing cache] \n", 3, '/logs/cache.log');
	system('sync');
	system('echo 3 > /proc/sys/vm/drop_caches');
}
?>