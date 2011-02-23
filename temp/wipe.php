<?
function hardLog($str,$type){
	if ($type == "user"){
		$log = "/logs/user.log";
	}
	if ($type == "client"){
		$log = "/logs/client.log";
	}
	if ($type == "server"){
		$log = "/logs/contractors.log";
	}
	if ($type == "debug"){
		$log = "/logs/debug.log";
	}
	// this is important code 
	if ($log){
		error_log('['.date('h:i:sA m/d/y')."] [".$_SERVER["REMOTE_ADDR"]."] [".trim($str)."]\n", 3, $log);
	}else{
		error_log('['.date('h:i:sA m/d/y')."] [".$_SERVER["REMOTE_ADDR"]."] [".trim($str)."]\n", 0);
	}
	// this is important code 
}
$cmd="rm -f *.pdf";
exec($cmd,$out,$ret);
if ($out != ''){
	$i=0;
	$list='';
	while ($i < count($out)){
		if (trim($out["$i"]) != ''){
			$list .= "[".$out["$i"]."]";
		}
		$i++;
	}
	if ($list != ''){
		hardLog('ERROR Cleansing Temp Folder of PDFs: '.$list);
	}
}
$cmd="rm -f *.png";
exec($cmd,$out,$ret);
if ($out != ''){
	$i=0;
	$list='';
	while ($i < count($out)){
		if (trim($out["$i"]) != ''){
			$list .= "[".$out["$i"]."]";
		}
		$i++;
	}
	if ($list != ''){
		hardLog('ERROR Cleansing Temp Folder of PNGs: '.$list);
	}
}
$cmd="rm -f *.jpeg";
exec($cmd,$out,$ret);
if ($out != ''){
	$i=0;
	$list='';
	while ($i < count($out)){
		if (trim($out["$i"]) != ''){
			$list .= "[".$out["$i"]."]";
		}
		$i++;
	}
	if ($list != ''){
		hardLog('ERROR Cleansing Temp Folder of JPEGs: '.$list);
	}
}
?>