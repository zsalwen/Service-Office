<?
include 'lock.php';
date_default_timezone_set('America/New_York');
$log[0] = "tail -40 /logs/user.log";
$log[1] = "tail -40 /logs/contractor.log";
$log[2] = "tail -40 /logs/client.log";
function buffer($str){
	$str = str_replace(date('m/j/y'),'',$str);
	$str = str_replace(date('m/d/y'),'',$str);
	$str = str_replace(date('n/j/y'),'',$str);
	$str = str_replace(date('n/d/y'),'',$str);
	$str = str_replace(date(']'),'',$str);
	$str = str_replace(date('['),'',$str);
	$str = str_replace($_SERVER[REMOTE_ADDR],'',$str);
	$str = str_replace('65.90.77.150','',$str); // burson IP
	$str = str_replace('69.250.7.16','',$str); // bwa ip
	$str = str_replace('216.82.241.35','',$str); // white ip
	$str = str_replace('208.73.110.130','',$str); // draper ip
	$str = str_replace('76.21.136.184','',$str); // castles ip
	
	return $str;
}
function getLog($log){
exec($log." 2>&1", $output);
foreach($output as $outputline) {
$obj = str_replace('\'','',trim($outputline));
if ($obj){
echo (buffer($obj)."\n");
}
}} ?><style>td{font-size:10px;}b{font-size:13px;}</style><table><tr><td colspan='3'><center><b>Live User Logs : <?=date('r')?> : 5 Second Refresh</b></center></td></tr><tr><td valign='top'><?=getLog($log[0]);?></td><td valign='top'><?=getLog($log[1]);?></td><td valign='top'><?=getLog($log[2]);?></td></tr></table>