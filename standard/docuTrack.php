<? 
session_start();
include 'common.php';

function opLog2($event){
	@mysql_query("insert into syslog (logTime, event) values (NOW(), '$event')");
}
$status ="Standard File docuTrack Terminal Loaded... Ready to scan barcode!";

if ($_POST[text1]){
$logic = explode('%',$_POST[text1]);
$packetDef=explode('-',$logic[0]);
$packet=$packetDef[0];
$defendant=strtoupper($packetDef[1]);
$server=$packetDef[2];

if ($_POST[document2]){
$_SESSION[document]=$_POST[document2];
$document = $_POST[document2];
}elseif($_POST[document]){
$_SESSION[document]=$_POST[document];
$document = $_POST[document];
}





@mysql_query("INSERT INTO docuTrack (packet, defendant, server, document, location, binder) values ('$packet','$defendant', '$server', '$document','".$_COOKIE[psdata][name]."', NOW() )");


hardLog("Packet ".$packet."-".$defendant."-".$server.": ".$_COOKIE[psdata][name]." docuTrack ".$document,'user');


opLog2("Packet ".$packet."-".$defendant."-".$server.": ".$_COOKIE[psdata][name]." docuTrack ".$document);
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
	 <table>
 <tr>
 
	<td><select name='document' onChange="form1.text1.focus()">
		<? if ($_SESSION[document]){ ?>
		<option><?=$_SESSION[document]?></option>
		<? } ?>
		<?
		$r247 = @mysql_query("select distinct document from docuTrack order by document");
		while($d247=mysql_fetch_array($r247,MYSQL_ASSOC)){
		?>
		<option><?=$d247[document]?></option>
		<? } ?>
		</select>or<input name="document2"></td>
	</tr>
 
</table> 
 
  <table>
<tr>
<? if(!$_GET[logic]){ ?>


	<td>Scan Document Barcode<br><div style="width:300px;height:50px"><input style="width:300px; height:50px;font-size:20px;" name="text1" onKeyUp="toCount('text1','sBann','{CHAR} characters left',100);" id="text1" value="" onChange="upda()" ></div></td>
<script>form1.text1.focus()</script>
<style>
input { background-color:ff0000;font-size:20px; width:300px; }
option {font-size:20px; }
select {font-size:20px; }
body { background-color:000000 }
td { background-color:ffffff; font-size: 20px; }
</style>
	<? } ?>
	</tr>
</table>
</form>
<div style="position:absolute; bottom:0px; right:0px; height:40px; font-size:30px; width:100%; background-color:999999; color:ff0000;">
<span id="sBann" class="minitext">100 bytes left.</span> - <?=$status?></div>

</form>


