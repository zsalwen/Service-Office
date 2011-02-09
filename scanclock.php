<?
// this page will use no security! 
function id2name($id){
mysql_select_db('service');
	$q="SELECT name FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[name];
}



if ($_GET[text1]){
$now=date('G.i');
$weekDay=strtoupper(date('l'));
mysql_connect();
mysql_select_db('service');
$parts = explode('-',$_GET[text1]);
$user_id = $parts[0];
$record = explode('%',$parts[1]);
$record = $record[0];

if($_COOKIE[psdata][user_id] == '1'){
		$q="INSERT INTO MDWestServeTimeClock (user_id, punch_time, punch_date, action, note) values
									('$user_id', NOW(), NOW(), '$record', '$note')";
		$r = @mysql_query($q);
		$msg = id2name($user_id).' '.$record;
		echo "<script>alert('$msg');</script>";


} elseif (($now < 8.3 || $now > 17.3 || $weekDay == 'SUNDAY' || $weekDay == 'SATURDAY') && $_POST[note] == ''){
		echo "<script>alert('MISSING OVERTIME NOTE!!!!  $record UNSUCCESSFUL.')</script>";
	}else{
		$q="INSERT INTO MDWestServeTimeClock (user_id, punch_time, punch_date, action, note) values
									('$user_id', NOW(), NOW(), '$record', '$note')";
		$r = @mysql_query($q);
		$msg = id2name($user_id).' '.$record;
		echo "<script>alert('$msg');</script>";
}

}


if ($_POST[text1]){
$now=date('G.i');
$weekDay=strtoupper(date('l'));
mysql_connect();
mysql_select_db('service');
$parts = explode('-',$_POST[text1]);
$user_id = $parts[0];
$record = explode('%',$parts[1]);
$record = $record[0];
$note=addslashes(strtoupper($_POST[note]));
	if (($now < 8.3 || $now > 17.3 || $weekDay == 'SUNDAY' || $weekDay == 'SATURDAY') && $_POST[note] == ''){
		echo "<script>alert('MISSING OVERTIME NOTE!!!!  $record UNSUCCESSFUL.')</script>";
	}else{
		$q="INSERT INTO MDWestServeTimeClock (user_id, punch_time, punch_date, action, note) values
									('$user_id', NOW(), NOW(), '$record', '$note')";
		$r = @mysql_query($q);
		$msg = id2name($user_id).' '.$record;
		echo "<script>alert('$msg');</script>";
	}
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
	  window.location.href='?badge='+document.form1.text1.value;
	}
    function upda2() {
	  string=""+document.form1.text1.value; 
	}
  //-->
  </script>

  <body onLoad="clear()">  
  <form action="scanclock.php" name="form1" method="POST" onSubmit="{calc(); return false;}">
  <center style="background-color:00FF00;">Overtime Notes:<br><textarea name="note" rows='2' cols='50'></textarea></center>
   <table>
	 <tr>
	<td colspan='6'>Scan Name Badge<br><div style="width:600px;height:100px"><input style="width:500px; height:100px;font-size:75px;" name="text1" onKeyUp="toCount('text1','sBann','{CHAR} characters left',100);" id="text1" value="" onChange="upda()" ></div></td>
<script>form1.text1.focus()</script>
<style>
input { background-color:ffff00 }
body { background-color:000000 }
td { background-color:ffffff; font-size: 30px; }
</style>
	</tr>
</table>
</form>

<div style="position:absolute; bottom:0px; right:0px; height:40px; font-size:30px; width:100%; background-color:999999; color:ff0000;">
<span id="sBann" class="minitext">100 characters left.</span> - <?=$status?></div>

</form>
<? if($_COOKIE[psdata][user_id]){ ?>
<center>
<table id="buttons">
<tr>
<td><?=$_COOKIE[psdata][name];?>:</td>
<td><a href="?text1=<?=$_COOKIE[psdata][user_id];?>-CLOCK IN">Clock In</a></td>
<td><a href="?text1=<?=$_COOKIE[psdata][user_id];?>-CLOCK OUT">Clock Out</td>
<td><a href="?text1=<?=$_COOKIE[psdata][user_id];?>-BREAK IN">Break In</td>
<td><a href="?text1=<?=$_COOKIE[psdata][user_id];?>-BREAK OUT">Break Out</td>
</tr>
</table>
</center>
<? } else{ ?>
<table id="buttons">
<tr>
<td>No User Logged In.</td>

</tr>
</table>
<? }?>