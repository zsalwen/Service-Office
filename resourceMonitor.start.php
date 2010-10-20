<?
mysql_connect();
mysql_select_db('core');
$mtimeResourceMonitorStart = microtime();
$mtimeResourceMonitorStart = explode (" ", $mtimeResourceMonitorStart);
$mtimeResourceMonitorStart = $mtimeResourceMonitorStart[1] + $mtimeResourceMonitorStart[0];
$tstartResourceMonitorStart = $mtimeResourceMonitorStart; 
function resourceMonitorvalueData($key){
  $r=@mysql_query("select valueData from config where keyData = '$key'");
  $d=mysql_fetch_array($r,MYSQL_ASSOC);
  return $d[valueData];
}
function resourceMonitortalk($to,$message){
  $username = 'mdwestserve@gmail.com';
  $password = resourceMonitorvalueData($username);
  @mysql_query("insert into talkQueue (fromAccount,fromPassword,toAddress,message,sendRequested,sendStatus) values ('$username','$password','$to','$message',NOW(),'ready to send')");
}
function resourceMonitorSearch($string,$search){
	$pos = strpos($string, $search);
	if ($pos === false) {
		return 0;
	} else {
		return 1;
	}
}
function resourceMonitorStartGetMemory() {
	$memory_last_line = exec('free |grep Mem',$memory);
	$full = str_replace(' ','.',$memory_last_line);
	$full = str_replace('Mem:.......','',$full);
	$full = str_replace('....','X',$full);
	$full = str_replace('.','',$full);
	$parts = explode('X',$full);
	//echo '<pre>'.$full.'</pre><hr>';
	$kb = $parts['2'];
	$mb = $kb / 1000; 
	$final = number_format($mb,0);
	$final = str_replace(',','',$final);
	return $final."MB";
}
function resourceMonitorLeading_zeros($value, $places){
// Function written by Marcus L. Griswold (vujsa)
// Can be found at http://www.handyphp.com
// Do not remove this header!
    if(is_numeric($value)){
        for($x = 1; $x <= $places; $x++){
            $ceiling = pow(10, $x);
            if($value < $ceiling){
                $zeros = $places - $x;
                for($y = 1; $y <= $zeros; $y++){
                    $leading .= "0";
                }
            $x = $places + 1;
            }
        }
        $output = $leading . $value;
    }
    else{
        $output = $value;
    }
    return $output;
}
function my_memory_usage() {
        $mem_usage = memory_get_usage(true);
        return round($mem_usage/1048576,10);
}   
function resourceMonitorStartServerResponse($page,$time,$query,$log,$debug){
	$load = exec("uptime");
	$load = split("load average:", $load);
	$load = split(", ", $load[1]);
	$load = resourceMonitorLeading_zeros(number_format((trim($load[0])*100)/2,0),2);
	$load = "$load%";
	$host = gethostbyaddr($_SERVER["REMOTE_ADDR"]);	
	$myMem = my_memory_usage();
	$speed = $myMem / $time;
	$loadTime = number_format($time,2);

	
	$swap = exec('swapon -s', $retval);
	$swap = explode('partition',$swap);
	$swap = trim($swap[1]);
	$swap = explode('	',$swap);
	
	
	
	if ($_COOKIE[psdata][name]){ $user = $_COOKIE[psdata][name]; }
	if ($_COOKIE[core][username]){ $user = $_COOKIE[core][username]; }
	if ($_COOKIE[portal][name]){ $user = $_COOKIE[portal][name]; }

	if (!$user){ $user =  "$host";}
	
	if (trim($page) != 'report.php'){ // don't log the toolbar
		$str = "[".date('m/d/Y h:i A')."] [$load] [".resourceMonitorStartGetMemory()."] [".$swap['1']."b] [".resourceMonitorLeading_zeros((number_format($speed,2)),3)."MB/s] [".$loadTime."s] [".trim($page)."] [".$user."]";
	if($loadTime > 10){
		resourceMonitortalk('insidenothing@gmail.com','Level 1 PHP Performance Alert: '.$page.' in '.$loadTime.'s');
		resourceMonitortalk('ron.mdwestserve@gmail.com','Level 1 PHP Performance Alert: '.$page.' in '.$loadTime.'s');
		resourceMonitortalk('zachsalwen@gmail.com','Level 1 PHP Performance Alert: '.$page.' in '.$loadTime.'s');
	}elseif($loadTime > 7){
		resourceMonitortalk('insidenothing@gmail.com','Level 2 PHP Performance Alert: '.$page.' in '.$loadTime.'s');
		resourceMonitortalk('zachsalwen@gmail.com','Level 2 PHP Performance Alert: '.$page.' in '.$loadTime.'s');
	}elseif($loadTime > 5){
		resourceMonitortalk('insidenothing@gmail.com','Level 3 PHP Performance Alert: '.$page.' in '.$loadTime.'s');
	}

		if ($debug && $_COOKIE[psdata][level] == 'Operations'){
			echo "<div style='border:double 10px #F00;'>$str<hr><b>psdata array</b>:<br><pre>";
			print_r($_COOKIE[psdata]);
			echo "</pre><hr><b>psportal array</b>:<br><pre>";
			print_r($_COOKIE[psportal]);
			echo "</pre><hr><b>portal array</b>:<br><pre>";
			print_r($_COOKIE[portal]);
			echo "</pre><hr><b>session array</b>:<br><pre>";
			print_r($_SESSION);
			echo "</pre><hr><b>get array</b>:<br><pre>";
			print_r($_GET);
			echo "</pre><hr><b>post array</b>:<br><pre>";
			print_r($_POST);
			echo "</pre></div>";
		}
		error_log($str."\n", 3, $log);
		error_log($str."\n", 3, '/logs/code/'.str_replace('/','-',trim($page)).'.log');
		error_log($str."\n", 3, '/logs/user/'.$user.'.log');
	$test1 = resourceMonitorSearch($page,'staff'); // 1 = staff page
	if ($test1 == 1 && !$_COOKIE['psdata']['name']){
	error_log($str."\n", 3, '/logs/fail.log');
	}
	
	}
	//error_log("[".date('h:iA m/d/y')."] [$load] [".resourceMonitorStartGetMemory()."] [".resourceMonitorLeading_zeros((number_format($speed,2)),3)."MB/s] [".$loadTime."s] [".trim($page)."] [$host ".$_COOKIE[psdata][name]." ".$_COOKIE[core][username]." ".$_COOKIE[portal][name]."] \n", 3, $log);
	//echo "<div align='center'>[".date('h:iA m/d/y')."] [".(number_format($speed,2))."MB/s] [".$loadTime."s] [$host ".$_COOKIE[psdata][name]." ".$_COOKIE[core][username]." ".$_COOKIE[portal][name]."]</div>";
}
?>