<? include 'common.php';
	//include 'menu.php';
function outOfState($packet_id){
	$q="SELECT state1, state1a, state1b, state1c, state1d, state1e from ps_packets WHERE packet_id = '$packet_id'";
	$r=@mysql_query($q) or die("Query: outOfState: $q<br>".mysql_error());
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$i=0;
	if (strtoupper($d[state1e]) != 'MD' && $d[state1e] != ''){ $i++; }
	if (strtoupper($d[state1d]) != 'MD' && $d[state1d] != ''){ $i++; }
	if (strtoupper($d[state1c]) != 'MD' && $d[state1c] != ''){ $i++; }
	if (strtoupper($d[state1b]) != 'MD' && $d[state1b] != ''){ $i++; }
	if (strtoupper($d[state1a]) != 'MD' && $d[state1a] != ''){ $i++; }
	if (strtoupper($d[state1]) != 'MD' && $d[state1] != ''){ $i++; }
	return $i;
}
function serverCount($packet_id){
	$q="SELECT server_id, server_ida, server_idb, server_idc, server_idd, server_ide from ps_packets WHERE packet_id = '$packet_id'";
	$r=@mysql_query($q) or die("Query: outOfState: $q<br>".mysql_error());
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$i=0;
	if ($d[server_id] != ''){ $i++; }
	if ($d[server_ida] != $d[server_id] && $d[server_ida] != ''){ $i++; }
	if ($d[server_idb] != $d[server_ida] && $d[server_idb] != $d[server_id] && $d[server_idb] != ''){ $i++; }
	if ($d[server_idc] != $d[server_idb] && $d[server_idc] != $d[server_ida] && $d[server_idc] != $d[server_id] && $d[server_idc] != ''){ $i++; }
	if ($d[server_idd] != $d[server_idc] && $d[server_idd] != $d[server_idb] && $d[server_idd] != $d[server_ida] && $d[server_idd] != $d[server_id] && $d[server_idd] != ''){ $i++; }
	if ($d[server_ide] != $d[server_idd] && $d[server_ide] != $d[server_idc] && $d[server_ide] != $d[server_idb] && $d[server_ide] != $d[server_ida] && $d[server_ide] != $d[server_id] && $d[server_ide] != ''){ $i++; }
	return $i;
}
function getAttID($packet_id){
	
	return $d[attorneys_id];
}
$status ="MDWestServe Awaiting Case # Terminal Loaded... Ready for Logic #!";
if ($_GET[logic]){
	$status = "Awaiting Case Number Marked for Packet ".$_GET[logic];
	$packet = explode('-',$_GET[logic]);
	$packet = $packet[0];
	$pos=stripos(strtoupper($_GET[logic]),"EV");
	if ($pos !== false){
		$packet=explode("EV",strtoupper($packet));
		$packet=$packet[1];
		$eviction=1;
	}
	if ($eviction == 1){
		$q="SELECT attorneys_id, filing_status, process_status, case_no, prepAlert, caseVerify, circuit_court from evictionPackets WHERE eviction_id = '$packet'";
	}else{
		$q="SELECT attorneys_id, filing_status, process_status, case_no, prepAlert, caseVerify, circuit_court from ps_packets WHERE packet_id = '$packet'";
		if (serverCount($packet) > 1){
			echo "<script>alert('File Contains Multiple Servers')</script>";
		}
	}
	$r=@mysql_query($q) or die("Query: eviction: $q<br>".mysql_error());
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d[case_no]){
		echo "<script>alert('".$d[case_no]."!!!!!!!!!!!!!!!')</script>";
	}
	/*
	if ($eviction != 1){
		if($d[attorneys_id] == 3){
			$q2="SELECT * FROM occNotices WHERE packet_id='$packet'";
			$r2=@mysql_query($q2) or die("Query: occNotices: $q2<br>".mysql_error());
			$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
			if ($d2[packet_id]){
				echo "<script>alert('WHITE FILE: DO NOT FORGET OCCUPANT NOTICE')</script>";
			}
		}elseif($d[attorneys_id] == 7){
			$q2="SELECT * FROM occNotices WHERE packet_id='$packet'";
			$r2=@mysql_query($q2) or die("Query: occNotices: $q2<br>".mysql_error());
			$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
			if ($d2[packet_id]){
				echo "<script>alert('LINOWES FILE: DO NOT FORGET OCCUPANT NOTICE')</script>";
			}
		}elseif($d[attorneys_id] == 68){
			$q2="SELECT * FROM occNotices WHERE packet_id='$packet'";
			$r2=@mysql_query($q2) or die("Query: occNotices: $q2<br>".mysql_error());
			$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
			if ($d2[packet_id]){
				echo "<script>alert('FEIDLER FILE: DO NOT FORGET OCCUPANT NOTICE')</script>";
			}
		}
	}
	if ($d[attorneys_id] == 1 && $eviction == 1 && $d[circuit_court] == 'PRINCE GEORGES'){
		echo "<script>alert('BURSON PG EVICTION! DO NOT FILE! MAIL TO BURSON: ATTENTION MAXINE SUAREZ!!!')</script>";
	}
	if ($d[process_status] == 'CANCELLED'){
		echo "<script>alert('FILE CANCELLED BY CLIENT')</script>";
	}
	if ($d[prepAlert] != ''){
		echo "<script>alert('Prep Alert: ".addslashes($d[prepAlert])."')</script>";
	}
	if ($d[attorneys_id] == 70){
		echo "<script>alert('BGW FILE: SEND TO CLIENT')</script>";
	}elseif($d[filing_status] == 'DO NOT FILE'){
		echo "<script>alert('DO NOT FILE')</script>";
	}
	
	if ($d[case_no] == '' || $d[case_no] == 'PENDING'){
		echo "<script>alert('MISSING CASE NUMBER!!!!!!')</script>";
	}else{
		if ($d[caseVerify] == ''){
			echo "<script>alert('UNVERIFIED CASE NUMBER!!!!!!')</script>";
		}
	}*/
	if ($eviction == 1){
		ev_timeline($packet,$_COOKIE[psdata][name]." Marked Affidavits AWAITING CASE NUMBER ".$_GET[logic]);
		$q="update evictionPackets set filing_status = 'AWAITING CASE NUMBER' where eviction_id = '$packet'";
	}else{
		timeline($packet,$_COOKIE[psdata][name]." Marked Affidavits AWAITING CASE NUMBER ".$_GET[logic]);
		$q="update ps_packets set filing_status = 'AWAITING CASE NUMBER' where packet_id = '$packet'";
	}
	@mysql_query($q) or die("Query: $q<br>".mysql_error());
	opLog($_COOKIE[psdata][name]." acnTerminal #$packet AWAITING CASE NUMBER");
	echo "<script>window.location.href='acnTerminal.php';</script>";
}
if ($_POST[text1]){
	echo "<script>window.location.href='acnTerminal.php?logic=$_POST[text1]';</script>";
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
	  toCount('text1','sBann','{CHAR} characters left',100);
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
 <br><br><br><br><br><br> <br><br><br><br><br><br>
  <table style="padding-left:150px;">
	 <form action="acnTerminal.php" name="form1" method="POST" onSubmit="{calc(); return false;}"><tr>
	<td colspan='6'>Scan Caseless'ed Affidavit<br><div style="width:600px;height:100px"><input style="width:1000px; height:100px;font-size:75px;" name="text1" onKeyUp="toCount('text1','sBann','{CHAR} characters left',100);" id="text1" value="" onChange="upda()" ></div></td>
<script>form1.text1.focus()</script>
<style>
input { background-color:ffff00 }
body { background-color:000000 }
td { background-color:ffffff; font-size: 50px; }
</style>
	</tr>
</table>
</form>
<center><a href='terminal.php'><h1>ReSeT</h1></a></center>
<div style="position:absolute; bottom:0px; right:0px; height:40px; font-size:30px; width:100%; background-color:999999; color:ff0000;">
<span id="sBann" class="minitext">100 characters left.</span> - <?=$status?></div>

</form>


