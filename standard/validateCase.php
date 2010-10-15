<?
mysql_connect();
function hardLog($str,$type){
	if ($type == "user"){
		$log = "/logs/user.log";
	}
	// this is important code 
	// this is important code 
	// this is important code 
	if ($log){
		error_log(date('h:iA j/n/y')." ".$_COOKIE[psdata][name]." ".trim($str)."\n", 3, $log);
	}
	// this is important code 
}
function talk($to,$message){
	include_once '/thirdParty/xmpphp/XMPPHP/XMPP.php';
	$conn = new XMPPHP_XMPP('talk.google.com', 5222, 'talkabout.files@gmail.com', '', 'xmpphp', 'gmail.com', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
	try {
		$conn->useEncryption(true);
		$conn->connect();
		$conn->processUntil('session_start');
		//$conn->presence("Ya, I'm online","available","talk.google.com");
		$conn->message($to, $message);
		$conn->disconnect();
	} catch(XMPPHP_Exception $e) {
		die($e->getMessage());
	}
}

function timeline($id,$note){
	error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [".trim($id)."] [".trim($note)."] \n", 3, '/logs/timeline.log');
	
	mysql_select_db ('core');
	//talk('insidenothing@gmail.com',"$note for standard packet $id");

	$q1 = "SELECT timeline FROM standard_packets WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	$d1 = mysql_fetch_array($r1, MYSQL_ASSOC);
	$access=date('m/d/y g:i A');
	if ($d1[timeline] != ''){
		$notes = $d1[timeline]."<br>$access: ".$note;
	}else{
		$notes = $access.': '.$note;
	}
	$notes = addslashes($notes);
	$q1 = "UPDATE standard_packets set timeline='$notes' WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	//@mysql_query("insert into syslog (logTime, event) values (NOW(), 'Packet $id: $note')");
}
?>
<form>
Case # <input name="case" value="<?=strtoupper($_GET['case'])?>" /><input type="submit" value="Test Case Number" /></form>
<font size="+2">
<?
function alert($str){ echo "<script>alert('$str')</script>"; die(); } 
function courtSelect($id,$case){
if ($id == "01"){ return "Allegany"; }
if ($id == "02"){ return "Anne Arundel"; }
if ($id == "03"){ return "Baltimore"; }
if ($id == "04"){ return "Calvert"; }
if ($id == "05"){ return "Caroline"; }
if ($id == "06"){ return "Carroll"; }
if ($id == "07"){ return "Cecil"; }
if ($id == "08"){ return "Charles"; }
if ($id == "09"){ return "Dorchester"; }
if ($id == "10"){ return "Frederick"; }
if ($id == "11"){ return "Garrett"; }
if ($id == "12"){ return "Harford"; }
if ($id == "13"){ return "Howard"; }
if ($id == "14"){ return "Kent"; }
if ($id == "17"){ return "Queen Anne's"; }
if ($id == "18"){ return "St. Mary's"; }
if ($id == "19"){ return "Somerset"; }
if ($id == "20"){ return "Talbot"; }
if ($id == "21"){ return "Washington"; }
if ($id == "22"){ return "Wicomico"; }
if ($id == "23"){ return "Worcester"; }
if ($id == "24"){ return "Baltimore City"; }
alert("Invalid County ID: $id, expecting 01-14, 17-24. ($case)");
}


if ($_GET[validate]){

mysql_select_db('core');
@mysql_query("update standard_packets set caseVerify='".$_COOKIE[psdata][name]."' where packet_id='$_GET[validate]'") or die(mysql_error());
timeline($_GET[validate],$_COOKIE[psdata][name]." verfied case number formatting.");
	hardLog('verfied case number formatting for '.$_GET[validate],'user');

?><script>window.parent.location.href='order.php?packet=<?=$_GET[validate]?>';</script><? 
}


if ($_GET['case']){

$case = strtoupper($_GET['case']);


$test1 = substr($case,0,1);
$test2 = strlen($case);


if ($test1 == "C"){
	$court = "PRINCE GEORGES";
}elseif($test2 == "6" || $test2 == "7"){
	$court = "MONTGOMERY";
}else{
	$court = "OTHER";
}




if ($court == "PRINCE GEORGES"){
	$len = strlen($case);
	if ($len != 11){ alert("Wrong Number of Characters: $len, expecting 11. ($case)");}
	$char = str_split($case);
	if (!preg_match('/C/',$char[0])){ alert("Invalid Character: $char[0], expecting C. ($case)"); }
	if (!preg_match('/A/',$char[1])){ alert("Invalid Character: $char[1], expecting A. ($case)"); }
	if (!preg_match('/E/',$char[2])){ alert("Invalid Character: $char[2], expecting E. ($case)"); }
	if (!preg_match('/[0-9]/',$char[3])){ alert("Invalid Character: $char[3], expecting 0-9. ($case)"); }
	if (!preg_match('/[0-9]/',$char[4])){ alert("Invalid Character: $char[4], expecting 0-9. ($case)"); }
	if (!preg_match('/-/',$char[5])){ alert("Invalid Character: $char[5], expecting -. ($case)"); }
	if (!preg_match('/[0-9]/',$char[6])){ alert("Invalid Character: $char[6], expecting 0-9. ($case)"); }
	if (!preg_match('/[0-9]/',$char[7])){ alert("Invalid Character: $char[7], expecting 0-9. ($case)"); }
	if (!preg_match('/[0-9]/',$char[8])){ alert("Invalid Character: $char[8], expecting 0-9. ($case)"); }
	if (!preg_match('/[0-9]/',$char[9])){ alert("Invalid Character: $char[9], expecting 0-9. ($case)"); }
	if (!preg_match('/[0-9]/',$char[10])){ alert("Invalid Character: $char[10], expecting 0-9. ($case)"); }
	echo "$case is a valid for <strong>Prince George's</strong> county.";
		if ($_GET[packet]){
	 	echo "<a href='?validate=$_GET[packet]'>Mark Packet $_GET[packet] Validated</a>";
	 }

}

if ($court == "MONTGOMERY"){
	$len = strlen($case);
	if ($len == 7){ 
		$char = str_split($case);
		if (!preg_match('/[0-9]/',$char[0])){ alert("Invalid Character: $char[0], expecting 0-9. ($case)"); }
		if (!preg_match('/[0-9]/',$char[1])){ alert("Invalid Character: $char[1], expecting 0-9. ($case)"); }
		if (!preg_match('/[0-9]/',$char[2])){ alert("Invalid Character: $char[2], expecting 0-9. ($case)"); }
		if (!preg_match('/[0-9]/',$char[3])){ alert("Invalid Character: $char[3], expecting 0-9. ($case)"); }
		if (!preg_match('/[0-9]/',$char[4])){ alert("Invalid Character: $char[4], expecting 0-9. ($case)"); }
		if (!preg_match('/[0-9]/',$char[5])){ alert("Invalid Character: $char[5], expecting 0-9. ($case)"); }
		if (!preg_match('/V/',$char[6])){ alert("Invalid Character: $char[6], expecting V. ($case)"); }
		echo "$case is a valid for Montgomery county.";
		if ($_GET[packet]){
	 	echo "<a href='?validate=$_GET[packet]'>Mark Packet $_GET[packet] Validated</a>";
	 }
	}elseif($len == 6){ 
		$char = str_split($case);
		if (!preg_match('/[0-9]/',$char[0])){ alert("Invalid Character: $char[0], expecting 0-9. ($case)"); }
		if (!preg_match('/[0-9]/',$char[1])){ alert("Invalid Character: $char[1], expecting 0-9. ($case)"); }
		if (!preg_match('/[0-9]/',$char[2])){ alert("Invalid Character: $char[2], expecting 0-9. ($case)"); }
		if (!preg_match('/[0-9]/',$char[3])){ alert("Invalid Character: $char[3], expecting 0-9. ($case)"); }
		if (!preg_match('/[0-9]/',$char[4])){ alert("Invalid Character: $char[4], expecting 0-9. ($case)"); }
		if (!preg_match('/[0-9]/',$char[5])){ alert("Invalid Character: $char[5], expecting 0-9. ($case)"); }
		echo "$case is a valid for <strong>Montgomery</strong> county.";
			if ($_GET[packet]){
	 	echo "<a href='?validate=$_GET[packet]'>Mark Packet $_GET[packet] Validated</a>";
	 }

	}else{
		alert("Wrong Number of Characters: $len, expecting 6 or 7. ($case)");
	}
}



if ($court == "OTHER"){
	$len = strlen($case);
	if ($len != 11){ alert("Wrong Number of Characters: $len, expecting 11. ($case)");}
	$char = str_split($case);
	if (!preg_match('/[0-9]/',$char[0])){ alert("Invalid Character: $char[0], expecting 0-9. ($case)"); }
	if (!preg_match('/[0-9]/',$char[1])){ alert("Invalid Character: $char[1], expecting 0-9. ($case)"); }
	if (!preg_match('/[A-Z]/',$char[2])){ alert("Invalid Character: $char[2], expecting A-Z. ($case)"); }
	if (!preg_match('/[0-9]/',$char[3])){ alert("Invalid Character: $char[3], expecting 0-9. ($case)"); }
	if (!preg_match('/[0-9]/',$char[4])){ alert("Invalid Character: $char[4], expecting 0-9. ($case)"); }
	if (!preg_match('/[0-9]/',$char[5])){ alert("Invalid Character: $char[5], expecting 0-9. ($case)"); }
	if (!preg_match('/[0-9]/',$char[6])){ alert("Invalid Character: $char[6], expecting 0-9. ($case)"); }
	if (!preg_match('/[0-9]/',$char[7])){ alert("Invalid Character: $char[7], expecting 0-9. ($case)"); }
	if (!preg_match('/[0-9]/',$char[8])){ alert("Invalid Character: $char[8], expecting 0-9. ($case)"); }
	if (!preg_match('/[0-9]/',$char[9])){ alert("Invalid Character: $char[9], expecting 0-9. ($case)"); }
	if (!preg_match('/[0-9]/',$char[10])){ alert("Invalid Character: $char[10], expecting 0-9. ($case)"); }
	$select = courtSelect($char[0].$char[1],$case);
	if ($select){
	echo "$case is a valid for <strong>$select</strong> county. ";
	if ($_GET[packet]){
	 	echo "<a href='?validate=$_GET[packet]'>Mark Packet $_GET[packet] Validated</a>";
	 }
	}else{
		echo "$case is NOT a valid for $select county.";
	}
	}

}
$select2=str_replace("'","",$select);
$select2=str_replace(".","",$select2);
$select2=strtoupper($select2);
if ($court == $_GET[county]){
	$bg="#99FF99";
}elseif($select2 == $_GET[county]){
	$bg="#99FF99";
}else{
	$bg="#FF0000";
}
?>
<style>
body{background-color:<?=$bg?>}
</style>
