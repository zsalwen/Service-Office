<?
// client invoice to print | email?



// common in each pdf
ini_set("memory_limit","12M");
//error_reporting(E_ALL);
set_time_limit(1800);
include '../class.ezpdf.php';
// let's set the page size and the margins
$pdf =& new Cezpdf('LETTER','portrait');
$pdf -> ezSetMargins(50,70,50,50);
function addNote($id,$note){
	$q1 = "SELECT notes FROM schedule_items WHERE schedule_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	$d1 = mysql_fetch_array($r1, MYSQL_ASSOC);
	$notes = $note.", ".$d1['notes'];
	$notes = addslashes($notes);
	$q1 = "UPDATE schedule_items set notes='$notes' WHERE schedule_id = '$id'";		
	//$r1 = @mysql_query ($q1) or die(mysql_error());
}

function dbout($full){
	$split = str_split($full);
	$out['year'] = $split[0].$split[1].$split[2].$split[3];
	$out['month'] = $split[4].$split[5];
	$out['day'] = $split[6].$split[7];
	return $out;
}

function db_connect($host,$database,$user,$password){
	$step1 = @mysql_connect ();
	$step2 = mysql_select_db ($database);
	return mysql_error();
}
db_connect('delta.mdwestserve.com','core','','');


function log_action($user_id,$action){
	$user_id = $_COOKIE['userdata']['user_id'];
	$q1 = "INSERT INTO activity_log (user_id, action, action_on) VALUES ( '$user_id', '$action', NOW() )";		
	//$r1 = @mysql_query ($q1) or die(mysql_error());
}
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

function defCount($packet_id){
	$c=0;
	$r=@mysql_query("SELECT name1, name2, name3, name4, name5, name6 from ps_packets WHERE packet_id='$packet_id'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d[name1]){$c++;}
	if ($d[name2]){$c++;}
	if ($d[name3]){$c++;}
	if ($d[name4]){$c++;}
	if ($d[name5]){$c++;}
	if ($d[name6]){$c++;}
	return $c;
}

// put a line top and bottom on all the pages, this is done in layers from top lowest built up
$all = $pdf->openObject();
$pdf->saveState();
// disable logo, prevent error
//$pdf->addJpegFromFile('../templates/logo3.jpg',0,$pdf->y-110,600,150); // add an image file in the common footer
$pdf->ezSetDy(-110); //gives us a buffer under the header
$pdf->setStrokeColor(0,0,0,1);
$pdf->line(20,20,578,20); // bottom line
$pdf->line(20,691,578,691); //top line
$pdf->restoreState();
$pdf->closeObject();
$pdf->addObject($all,'all'); // place on all pages
// define the font's here
$mainFont = '/fonts/verdana.ttf';
$codeFont = '/fonts/verdana.ttf';
// set the font
$pdf->selectFont($mainFont);
$data = array();
$pdf->openHere('Fit'); // this set's where we want to open the pdf and at what zoom
$pdf->line(20,636,578,636);  // above attorney
$pdf->line(20,582,578,582); // under attorney
//$pdf->line(300,570,300,510); // LINE BETWEEN ADDRESS AND DETAILS
$pdf->line(20,582,578,582); // under attorney
//$pdf->line(20,510,578,510); // under address
$pdf->line(20,570,578,570); // under desc / totals
$pdf->line(20,172,578,172); // under advertising
$pdf->line(20,160,578,160); // under amount due
$pdf->addTextWrap(115,701,500,42,'MDWestServe, Inc.');
$pdf->addTextWrap(120,651,500,24,'INVOICE: PROCESS SERVING');

$id=$_GET[id];
$q = "SELECT * FROM ps_packets, attorneys WHERE ps_packets.attorneys_id = attorneys.attorneys_id AND packet_id = '$id'";
$r = @mysql_query ($q) or die(mysql_error());
$data = mysql_fetch_array($r, MYSQL_ASSOC);

if ($data['bill410']){



$mail_address = explode('-',$data['address']);
$mail_address1 = trim($mail_address[0]);
$mail_address2 = trim($mail_address[1]);
$mail_address3 = trim($mail_address[2]);
$mail_address4 = trim($mail_address[3]);

$a = "SELECT name FROM ps_users where id = '".$data['server_id']."'";
$b = @mysql_query($a);
$c = mysql_fetch_array($b, MYSQL_ASSOC);

$pdf->addText(200,12,8,'MDWestServe, Inc. - '.date('l, F jS Y g:iA'));

$pdf->addTextWrap(20,622,300,12,$mail_address1);								
$pdf->addTextWrap(20,610,300,12,$mail_address2);								
$pdf->addTextWrap(20,598,300,12,$mail_address3);								
$pdf->addTextWrap(20,586,300,12,$mail_address4);								


$pdf->addTextWrap(305,618,300,14,'Server Packet: #'.$data['packet_id']);								
$pdf->addTextWrap(305,604,300,14,'File Number: '.$data['client_file']);								
$pdf->addTextWrap(305,590,300,14,'Civil Case Number: '.$data['case_no']);								




$pdf->addTextWrap(40,572,300,10,'SERVICE');								
$pdf->addTextWrap(500,572,300,10,'TOTALS');								




$total=0;


$pdf->addTextWrap(210,572,300,8,'FILE RECEIVED: '.$data['date_received']);								


if ($data['bill410']){
$pdf->addTextWrap(40,500,300,25,'SERVICE:');
		$pdf->addTextWrap(500,500,300,25,'$'.number_format($data['bill410'],2));	
}
if ($data['bill420']){
$pdf->addTextWrap(40,450,300,25,'MAIL:');
		$pdf->addTextWrap(500,450,300,25,'$'.number_format($data['bill420'],2));	
}
if ($data['bill430']){
$pdf->addTextWrap(40,400,300,25,'FILE:');
		$pdf->addTextWrap(500,400,300,25,'$'.number_format($data['bill430'],2));	
}
if ($data['bill440']){
$pdf->addTextWrap(40,350,300,25,'TRACE:');
		$pdf->addTextWrap(500,350,300,25,'$'.number_format($data['bill440'],2));	
}

		
if ($data['client_check']){		
$pdf->addTextWrap(40,320,300,10,'Payment using check: #'.$data['client_check']);
		if ($data['code410']){ $pdf->addTextWrap(400,320,300,10,'Paid Service: -$'.number_format($data['code410'],2)); }
		if ($data['code420']){ $pdf->addTextWrap(400,310,300,10,'Paid Mail: -$'.number_format($data['code420'],2));	}
		if ($data['code430']){ $pdf->addTextWrap(400,300,300,10,'Paid File: -$'.number_format($data['code430'],2));	}
		if ($data['code440']){ $pdf->addTextWrap(400,290,300,10,'Paid Trace: -$'.number_format($data['code440'],2));}	
}
if ($data['client_checka']){		
$pdf->addTextWrap(40,280,300,10,'Payment using check: #'.$data['client_checka']);
		if ($data['code410a']){ $pdf->addTextWrap(400,280,300,10,'Paid Service: -$'.number_format($data['code410a'],2)); }
		if ($data['code420a']){ $pdf->addTextWrap(400,270,300,10,'Paid Mail: -$'.number_format($data['code420a'],2));}	
		if ($data['code430a']){ $pdf->addTextWrap(400,260,300,10,'Paid File: -$'.number_format($data['code430a'],2));}	
		if ($data['code440a']){ $pdf->addTextWrap(400,250,300,10,'Paid Trace: -$'.number_format($data['code440a'],2));}	
}
if ($data['client_checkb']){		
$pdf->addTextWrap(40,240,300,10,'Payment using check: #'.$data['client_checkb']);
		if ($data['code410b']){ $pdf->addTextWrap(400,240,300,10,'Paid Service: -$'.number_format($data['code410b'],2)); }
		if ($data['code420b']){ $pdf->addTextWrap(400,230,300,10,'Paid Mail: -$'.number_format($data['code420b'],2));	}
		if ($data['code430b']){ $pdf->addTextWrap(400,220,300,10,'Paid File: -$'.number_format($data['code430b'],2));	}
		if ($data['code440b']){ $pdf->addTextWrap(400,210,300,10,'Paid Trace: -$'.number_format($data['code440b'],2));	}
}		
		
		
		



$pdf->addTextWrap(40,162,300,10,$data['service_status']." DUE:");
$pdf->addTextWrap(500,162,300,10,'$'.number_format($data['bill410']+$data['bill420']+$data['bill430']+$data['bill440']-$data[client_paid]-$data[client_paida]-$data[client_paidb],2));			

$pdf->addTextWrap(300,150,300,12,'MAKE ALL CHECKS PAYABLE TO');
$pdf->addTextWrap(340,138,300,12,'MDWestServe, Inc.');						
$pdf->addTextWrap(340,126,300,12,'300 East Joppa Road');						
$pdf->addTextWrap(340,114,300,12,'Hampton Plaza - Suite 1103');
$pdf->addTextWrap(340,102,300,12,'Baltimore, MD 21286');

//Place Logo
$logo='smallLogo.jpg';
$pdf->addJpegFromFile($logo,80,30,120,120);

$pdfcode = $pdf->ezOutput();

$dir = '/data/service/invoices';
if (!file_exists($dir)){
	mkdir ($dir,0777);
}

$dir = '/data/service/invoices/'.$data["display_name"]; //"data directory", best idea yet!
$dir2 = 'invoices/'.$data["display_name"];
// proper dir
if (!file_exists($dir)){
	mkdir ($dir,0777);
}
// 
if ($data["attorneys_id"] == 1 || $data["attorneys_id"] == 44){
$filename = $data["client_file"].'-'.$data["date_received"]."-"."CLIENT.".time().".PDF";
}else{
$filename = $data["case_no"]."-"."CLIENT.".time().".PDF";
}

$fname = $dir.'/'.$filename;

//echo "<h1>$fname</h1>";
$fp = fopen($fname, 'w');
fwrite($fp, $pdfcode);
fclose($fp);

$invoiceName = str_replace('invoices','serviceInvoices',$dir).'/'.$filename;
$invoiceName = str_replace('/data/service/','/',$invoiceName);
//echo "<h1>$invoiceName</h1>";

if($_COOKIE[psdata][name]){
	$cookieName=$_COOKIE[psdata][name];
}elseif($user[name]){
	$cookieName=$user[name];
}
$attid=$data[attorneys_id];
$q="insert into SIVC (attorneys_id,packetID,dataFile,url,savedby,stored) values ('$attid','$id','$fname','$invoiceName','$cookieName',NOW())";
@mysql_query($q) or die(mysql_error());




?>

Your invoice (#<?=mysql_insert_id()?>) is loading in pop-up window. 
<script>window.open('<?=$invoiceName?>',   'invoice<?=$id?>',   'width=600, height=800'); </script>


<? } else{ // we have no billing information 
		$headers  = "MIME-Version: 1.0 \n";
		$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
		$headers .= "From: service@mdwestserve.com \n";
		$headers .= "Cc: MDWS Archive <mdwestserve@gmail.com> \n";
		hardLog('REQUESTING INVOICE FOR PACKET '.$_GET[id],'user');
		mail('service@mdwestserve.com','INVOICE DETAILS REQUESTED FOR '.$_GET[id],'INVOICE INFORMATION REQUIRED FOR PACKET '.$_GET[id].' WITHIN 24 HOURS OF THIS REQUEST<br><a href="http://staff.mdwestserve.com/otd/order.php?packet='.$_GET[id].'">ENTER NOW</a>',$headers);
		echo 'INVOICE INFORMATION FOR PACKET '.$_GET[id].' WILL BE AVAILABLE WITHIN 24 HOURS OF THIS REQUEST.';
}?>