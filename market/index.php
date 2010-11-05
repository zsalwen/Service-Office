<?
// connect
mysql_connect();
mysql_select_db('core');
// build resources
$today=date('Y-m-d');
$r1 = @mysql_query("select * from market where type = 'attorney' AND coldCall <> '$today' AND phase='COLD CALL' AND doNotCall <> 'checked' ORDER BY name ASC");
$r2 = @mysql_query("select * from market where type = 'attorney' AND coldCall = '$today' AND phase='COLD CALL' AND doNotCall <> 'checked' ORDER BY name ASC");
$r3 = @mysql_query("select * from market where type = 'attorney' AND phase <> 'COLD CALL' AND phase <> 'CALL BACK' AND doNotCall <> 'checked' ORDER BY callBack ASC");
$r4 = @mysql_query("select * from market where type = 'attorney' AND phase='CALL BACK' ORDER BY name ASC");
// build html list
while ($d1=mysql_fetch_array($r1,MYSQL_ASSOC)){
	$h1 .= "<li><a href='details.php?id=$d1[marketID]'>$d1[name]</a></li>";
	$h1a .= "<li>$d1[coldCall]</li>";
	$phClass=explode(' ',$d1[phase]);
	$phClass=$phClass[0];
	if ($d1[phase] == 'CALL BACK'){
		$h1b .= "<li class='$phClass'>$d1[phase]-$d1[callBack]</li>";
	}else{
		$h1b .= "<li class='$phClass'>$d1[phase]</li>";
	}
}
while ($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){
	$h2 .= "<li><a href='details.php?id=$d2[marketID]'>$d2[name]</a></li>";
	$phClass=explode(' ',$d2[phase]);
	$phClass=$phClass[0];
	if ($d2[phase] == 'CALL BACK'){
		$h2a .= "<li class='$phClass'>$d2[phase]-$d2[callBack]</li>";
	}else{
		$h2a .= "<li class='$phClass'>$d2[phase]</li>";
	}
}
while ($d3=mysql_fetch_array($r3,MYSQL_ASSOC)){
	$h3 .= "<li><a href='details.php?id=$d3[marketID]'>$d3[name]</a></li>";
	$phClass=explode(' ',$d3[phase]);
	$phClass=$phClass[0];
	if ($d3[phase] == 'CALL BACK'){
		$h3a .= "<li class='$phClass'>$d3[phase]-$d3[callBack]</li>";
	}else{
		$h3a .= "<li class='$phClass'>$d3[phase]</li>";
	}
	$h3b .= "<li class='$phClass'>$d3[coldCall]</li>";
}
while ($d4=mysql_fetch_array($r4,MYSQL_ASSOC)){
	$h4 .= "<li><a href='details.php?id=$d4[marketID]'>$d4[name]</a></li>";
	$phClass=explode(' ',$d4[phase]);
	$phClass=$phClass[0];
	$h4a .= "<li class='$phClass'>$d4[phase]-$d4[callBack]</li>";
	$h4b .= "<li class='$phClass'>$d4[coldCall]</li>";
}
$today=date('m/d/Y');
if ($_GET[msg]){
	echo "<table align='center'><tr><td align='center'>$msg</td></tr></table>";
}
?>
<style>
.COLD {background-color:99FF55;}
.SEND {background-color:FFFFBB;}
.CALL {background-color:FF8800}
.GOOD {background-color:blue; color;FFFFFF;}
</style>


<table border="1" align="center">
	<tr>
		<td>Attorneys (Not Yet Cold Called On <?=$today?>)</td><td>Last Called On</td><td>Next Action</td>
	</tr>
	<tr>
		<td valign="top"><?=$h1;?></td><td valign="top"><?=$h1a;?></td><td valign='top'><?=$h1b?></td>
	</tr>
	<tr>
		<td>Attorneys Requiring Call Back</td><td>Next Action</td><td>Last Called</td>
	</tr>
	<tr>
		<td valign="top"><?=$h4;?></td><td valign='top'><?=$h4a?></td><td valign='top'><?=$h4b?></td>
	</tr>
	<tr>
		<td colspan='2'>Attorneys Cold Called Today, <?=$today?></td><td>Next Action</td>
	</tr>
	<tr>
		<td valign="top" colspan='2'><?=$h2;?></td><td valign='top'><?=$h2a?></td>
	</tr>
	<tr>
		<td>Attorneys Requiring Other Action</td><td>Next Action</td><td>Last Called</td>
	</tr>
	<tr>
		<td valign="top"><?=$h3;?></td><td valign='top'><?=$h3a?></td><td valign='top'><?=$h3b?></td>
	</tr>
</table>