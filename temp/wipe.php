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
	}
	// this is important code 
}
hardLog('Cleansing Temp Folder of PDFs, JPEGs & PNGs','user');
$cmd="rm -f *.pdf";
exec($cmd,$out,$ret);
$i=0;
echo "$cmd: OUT: [";
while ($i < count($out)){
	echo $out["$i"];
	$i++;
}
echo "] RET: [$ret]<hr>";
$cmd="rm -f *.png";
exec($cmd,$out,$ret);
$i=0;
echo "$cmd: OUT: [";
while ($i < count($out)){
	echo $out["$i"];
	$i++;
}
echo "] RET: [$ret]<hr>";
$cmd="rm -f *.jpeg";
exec($cmd,$out,$ret);
$i=0;
echo "$cmd: OUT: [";
while ($i < count($out)){
	echo $out["$i"];
	$i++;
}
echo "] RET: [$ret]<hr>";
?>