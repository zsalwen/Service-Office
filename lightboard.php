<? session_start(); ?>
<!-- Designed for 22" widescreen -->
<link rel="stylesheet" type="text/css" href="fire.css" />
<script>
function getWidth(items) {
  var myWidth = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myWidth = window.innerWidth;
  } else if( document.documentElement && ( document.documentElement.clientWidth ) ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
  } else if( document.body && ( document.body.clientWidth ) ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
  }
  return myWidth/items;
}
</script>
<style>
tr	{ background-color:transparent;	}
table { padding: 0px; }
a { background-color:#FFFFFF; font-size:14px; height:14px; overflow:auto; }
</style>
<div style='height:100%;overflow:auto;'>
<table border="0" width="100%" height="99%" cellspacing="0" cellpadding="0">
	<tr>
		<td style="background-color:#FFFFFF;"><iframe id="test" name="test" frameborder="0" height="95%" src='http://mdwestserve.com/affidavits/test.php?id=<?=$_GET[packet]?>'></iframe>

<? if(strpos($_GET[packet],"EV")!== false){
	$packetType='eviction';
	$eviction=str_replace("EV","",$_GET[packet]);
	$mark="Mark Eviction Packet <a href='http://staff.mdwestserve.com/ev/order.php?packet=$eviction' target='_blank'>$_GET[packet]</a> Filed By Staff on $_SESSION[fileDate]";
}else{
	$packetType='presale';
	$mark="Mark Presale Packet <a href='http://staff.mdwestserve.com/otd/order.php?packet=$_GET[packet]' target='_blank'>OTD$_GET[packet]</a> Filed By Staff on $_SESSION[fileDate]";
} 
echo "<div style='background-color:#FFFFFF; font-size:16px; font-variant:small-caps; height:16px; overflow:auto;'>$mark</div></td>";
mysql_connect();
mysql_select_db('core');
$i=0;
$q5="SELECT * FROM ps_affidavits WHERE packetID = '$_GET[packet]' order by defendantID";
$r5=@mysql_query($q5) or die ("Query: $q5<br>".mysql_error());
while ($d5=mysql_fetch_array($r5, MYSQL_ASSOC)){
	$i++;	
	$list .= "<script>window.frames['frame".$i."'].location='".str_replace('ps/','',$d5[affidavit])."';</script>";
	$table["$i"] = "<td align='center'><a target='frame".$i."' href='".str_replace('ps/','',$d5[affidavit])."'><strong>".$d5[defendantID]."</strong>: $d5[method]</a><br><iframe id='frame$i' name='frame$i' frameborder='0' height='97%' width='100%'></iframe></td>";
}
$items=$i+1;
$break=floor($i/2);
if ($break >= 1){
	$items=ceil($items/2);
}
if ($items > 3){
	$items=3;
}
$i=0;
$count=count($table);
$jsList = "<script>document.getElementById('test').width=getWidth($items);</script>";
//construct table, inserting new row halfway through, also js to resize based off browser window
while ($i < $count){$i++;
	$tableList .= $table["$i"];
	$jsList .= "<script>document.getElementById('frame$i').width=getWidth($items);</script>";
	if ($i == $break){
		$tableList .= "</tr><tr>";
	}
}
echo "$tableList</tr></table>$jsList</div>$list";
// We need an alert for a few exceptions
if ($packetType == 'presale'){
	$r=@mysql_query("select * from ps_packets where packet_id = '$_GET[packet]'");
}else{
	$packet=str_replace('EV','',$_GET[packet]);
	$r=@mysql_query("select * from evictionPackets where eviction_id = '$packet'");
}
$d=mysql_fetch_array($r,MYSQL_ASSOC);
if (strtoupper($d[state1a]) != 'MD' && $d[state1a] != ''){ echo "<script>alert('First possible place of abode is out of state.');</script>"; }
if (strtoupper($d[state1b]) != 'MD' && $d[state1b] != ''){ echo "<script>alert('Second possible place of abode is out of state.');</script>"; }
if (strtoupper($d[state1c]) != 'MD' && $d[state1c] != ''){ echo "<script>alert('Third possible place of abode is out of state.');</script>"; }
if (strtoupper($d[state1d]) != 'MD' && $d[state1d] != ''){ echo "<script>alert('Fourth possible place of abode is out of state.');</script>"; }
if (strtoupper($d[state1e]) != 'MD' && $d[state1e] != ''){ echo "<script>alert('Fifth possible place of abode is out of state.');</script>"; }
if ($d[name1] && !$d[onAffidavit1]){ echo "<script>alert('First person to serve is not a defendant on affidavit header.');</script>"; }
if ($d[name2] && !$d[onAffidavit2]){ echo "<script>alert('Second person to serve is not a defendant on affidavit header.');</script>"; }
if ($d[name3] && !$d[onAffidavit3]){ echo "<script>alert('Third person to serve is not a defendant on affidavit header.');</script>"; }
if ($d[name4] && !$d[onAffidavit4]){ echo "<script>alert('Fourth person to serve is not a defendant on affidavit header.');</script>"; }
if ($d[name5] && !$d[onAffidavit5]){ echo "<script>alert('Fifth person to serve is not a defendant on affidavit header.');</script>"; }
if ($d[name6] && !$d[onAffidavit6]){ echo "<script>alert('Sixth person to serve is not a defendant on affidavit header.');</script>"; }
?>