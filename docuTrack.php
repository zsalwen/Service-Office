<? 
session_start();
include 'common.php';

function opLog2($event){
	@mysql_query("insert into syslog (logTime, event) values (NOW(), '$event')");
}
function strpos2($hay,$nd1,$nd2){
	$hay=strtoupper($hay);
	if ((strpos($hay,$nd1) !== false) && (strpos($hay,$nd2) !== false)){
		return true;
	}else{
		return false;
	}
}
function isOp($server){
	$r=@mysql_query("SELECT level FROM ps_users WHERE id='$server' LIMIT 0,1");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[level] == 'Operations'){
		return true;
	}else{
		return false;
	}
}
$status ="docuTrack Terminal Loaded... Ready to scan barcode!";

if ($_POST[text1]){
$logic = explode('%',$_POST[text1]);
$packetDef=explode('-',$logic[0]);
$packet=$packetDef[0];
$defendant=strtoupper($packetDef[1]);
$server=$packetDef[2];
	$today=date('Y-m-d');
	$q="SELECT * FROM docuTrack WHERE packet='$packet' AND defendant='$defendant' AND server='$server' AND document='$_POST[document]' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[trackID]){
		$scanDate=explode(' ', $d[binder]);
		$scanDate=$scanDate[0];
		if ($scanDate != $today){
			echo "<script>alert('PREVIOUSLY DOCUTRACKED ON $scanDate')</script>";
		}
	}
	if (strpos2(strtoupper($packet),'EV','S') !== true){
		$r1=@mysql_query("SELECT rush, estFileDate FROM ps_packets WHERE packet_id='$packet' LIMIT 0,1");
		$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
		if ($d1[rush] != '' && $_POST[document] == 'SIGNED AFFIDAVIT' && isOp($server) !== true){
			echo "<script>alert('RUSH FILE!')</script>";
			$to = "RUSH SERVICE <service@mdwestserve.com>";
			$subject = "!!!!RUSH UPDATE FOR OTD$packet: DOCUTRACKED '$_POST[document]'!!!!";
			$headers  = "MIME-Version: 1.0 \n";
			$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
			$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email].">  \n";
			$body = $subject."<br>ESTIMATED CLOSE DATE: $d1[estFileDate]<hr><a href='http://staff.mdwestserve.com/otd/order.php?packet=$packet'>View Order Page</a>";
			mail($to,$subject,$body,$headers);
		}
	}

$_SESSION[document]=$_POST[document];





@mysql_query("INSERT INTO docuTrack (packet, defendant, server, document, location, binder) values ('$packet','$defendant', '$server', '$_POST[document]','".$_COOKIE[psdata][name]."', NOW() )");


hardLog("Packet ".$packet."-".$defendant."-".$server.": ".$_COOKIE[psdata][name]." docuTrack ".$_POST[document],'user');


opLog2("Packet ".$packet."-".$defendant."-".$server.": ".$_COOKIE[psdata][name]." docuTrack ".$_POST[document]);
?><script>window.location.href='docuTrack.php</script><?
}

?>

<script>

function getObject(obj) {
  var theObj;
  if(document.all) {
    if(typeof obj=="string") {
      return document.all(obj);
    } else {
      return obj.style;
    }
  }
  if(document.getElementById) {
    if(typeof obj=="string") {
      return document.getElementById(obj);
    } else {
      return obj.style;
    }
  }
  return null;
}

function toCheck(entrance) {
  var entranceObj=getObject(entrance);
  var mystring=entranceObj.value;

if (mystring.match(/%$/)) {
	//alert("match");
	form1.submit();
	}

  
;
}


function toCount(entrance,exit,text,characters) {
  var entranceObj=getObject(entrance);
  var exitObj=getObject(exit);
  var length=characters - entranceObj.value.length;
  
	toCheck(entrance);
 
 if(length == 80) {
	//alert('ping');
		var uri;
		uri ='?art='+document.form1.text2.value+'&logic2=<?=$_GET[logic]?>';

	  window.location.href=uri;	}
  
  if(length <= 0) {
    length=0;
    text='<span class="disable"> '+text+' </span>';
    entranceObj.value=entranceObj.value.substr(0,characters);
    }
  exitObj.innerHTML = text.replace("{CHAR}",length);
  
}

</script>

<script language="JavaScript">
  <!--
    string="";
    function app(cc) {
      string+=cc;
      document.form1.text1.value=string;
	  toCount('text1','sBann','{CHAR} bytes left',100);
    }
    function clear() {
      string="";
      document.form1.text1.value=string;
    }
    function calc() {
      if(string.length > 0) {
        inp="out="+string;
        eval(inp);
      } else out="0";
      document.form1.text1.value=out;
      string=""+out;
	  
    }
    function upda() {
	  string=""+document.form1.text1.value; 
	  window.location.href='?logic='+document.form1.text1.value;
	}
    function upda2() {
	  string=""+document.form1.text1.value; 
	}
  //-->
  </script>
  <body onLoad="clear()">  
	 <form action="docuTrack.php" name="form1" method="POST" onSubmit="{calc(); return false;}">
	 <br><br><br><br><table align='center'>
 <tr>
 
	<td><select name='document' onChange="form1.text1.focus()">
		<? if ($_SESSION[document] == 'OUT WITH COURIER' || $_SESSION[document] == 'OUT WITH SERVER-FBS' || $_SESSION[document] == 'SIGNED AFFIDAVIT' || $_SESSION[document] == 'FILED AFFIDAVIT'  || $_SESSION[document] == 'NOTARIZED AFFIDAVIT'){ ?>
		<option><?=$_SESSION[document]?></option>
		<? } ?>
		<option>OUT WITH COURIER</option>
		<option>OUT WITH SERVER-FBS</option>
		<option>SIGNED AFFIDAVIT</option>
		<option>FILED AFFIDAVIT</option>
		<option>NOTARIZED AFFIDAVIT</option>
		</select></td>
	</tr>
 
</table> 
 
  <table style="padding-left:150px;">
<tr>
<? if(!$_GET[logic]){ ?>


	<td>Scan Document Barcode<br><div style="width:600px;height:100px"><input style="width:1000px; height:100px;font-size:75px;" name="text1" onKeyUp="toCount('text1','sBann','{CHAR} characters left',100);" id="text1" value="" onChange="upda()" ></div></td>
<script>form1.text1.focus()</script>
<style>
input { background-color:ff0000 }
option {font-size:50px; }
select {font-size:50px; }
body { background-color:000000 }
td { background-color:ffffff; font-size: 50px; }
</style>
	<? } ?>
	</tr>
</table>
</form>
<div style="position:absolute; bottom:0px; right:0px; height:40px; font-size:30px; width:100%; background-color:999999; color:ff0000;">
<span id="sBann" class="minitext">100 bytes left.</span> - <?=$status?></div>

</form>


