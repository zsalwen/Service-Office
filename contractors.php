<? 
include 'common.php';
function currentFiles($id){
	$qt="SELECT name1, name2, name3, name4, name5, name6, process_status from ps_packets WHERE (server_id = '$id' OR server_ida = '$id' OR server_idb = '$id' OR server_idc = '$id' OR server_idd = '$id' OR server_ide = '$id') AND (process_status='READY' OR process_status='ASSIGNED')";
	$rt=@mysql_query($qt) or die("Query: $qt<br>".mysql_error());
	while($dt=mysql_fetch_array($rt, MYSQL_ASSOC)){
		if ($dt[process_status] == 'READY' || $dt[process_status] == 'ASSIGNED'){
			$assignedFiles++;
			if ($dt[name1]){ $assignedDefendants++; }
			if ($dt[name2]){ $assignedDefendants++; }
			if ($dt[name3]){ $assignedDefendants++; }
			if ($dt[name4]){ $assignedDefendants++; }
			if ($dt[name5]){ $assignedDefendants++; }
			if ($dt[name6]){ $assignedDefendants++; }
		}
	}
	if ($assignedFiles){
		$assignedStr = $assignedFiles.' Files / '.$assignedDefendants.' Defendants';
	}else{
		$assignedStr = "n/a";
	}
	return $assignedStr;
}
function numRows($status,$id){
	$r=mysql_query("SELECT packet_id FROM ps_packets WHERE service_status='$status' AND server_id='$id'");
	return mysql_num_rows($r);
}
function contractDisplay($str){
	if (strtoupper($str) == 'YES'){
		return "<span style='color:green;font-weight:bold;'>ACTIVE</span>";
	}elseif(strtoupper($str) == 'NO'){
		return "<span style='color:red;font-weight:bold;'>INACTIVE</span>";
	}
}
if ($_COOKIE[psdata][level] != "Operations"){
	$event = 'contractors.php';
	$email = $_COOKIE[psdata][email];
	$q1="INSERT into ps_security (event, email, entry_time) VALUES ('$event', '$email', NOW())";
	//@mysql_query($q1) or die(mysql_error());
	header('Location: router.php');
}
?>
<style>
a{border-style:hidden; text-decoration:none;}
</style>
<br />
<table border="1" cellpadding="3" width="100%" style="border-collapse:collapse;">
<?
$i=0;
$used = 0;
$q="SELECT id, company, name, last_login, manager_review, contract, DATE_FORMAT(last_login,'%a, %b %D %Y at %r') as login FROM ps_users where level <> 'Administrator' and level <> 'DELETED' and contract = 'YES' ORDER BY name";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error()); 
?>
	<tr bgcolor='#ccffcc'>
		<td nowrap>Server Name / Company Name</td>
        <td>Manager Review Notes</td>
		<td>Status</td>
    </tr>
<? while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){$i++;
/*$mandp='';
$pd='';
$total='';
$src='';
$mandp=numRows("MAILING AND POSTING",$d[id]);
$pd=numRows("PERSONAL DELIVERY",$d[id]);
$mandp2=$mandp;
$pd2=$pd;
$total=$mandp+$pd;
$mandp=number_format(($mandp/$total)*100,1);
$pd=number_format(($pd/$total)*100,1);
$src = "http://chart.apis.google.com/chart?chs=180x50&amp;chd=t:$mandp,$pd&amp;cht=p3&amp;chl=MP-$mandp2|PD-$pd2";
$title=$d[name]."'s File Performance: $mandp/$pd% (for $total files)";
$src2 = "http://chart.apis.google.com/chart?chs=800x350&amp;chd=t:$mandp,$pd&amp;cht=p3&amp;chl=Mailing and Posting: $mandp2|Personal Delivery: $pd2&chtt=$title";*/
 ?>
	<tr bgcolor="<?=$level?>" >
    	<td nowrap><?=$i ?>) <a href='contractor_profile.php?admin=<?=$d[id]?>'><?=$d[name]?><br><? if($d[company] != '' && ($d[company] != $d[name] && $d[company] != strtoupper($d[name]))){ echo '<small>'.$d[company].'</small>';}?></a></td>
        <td><small><?=stripslashes($d[manager_review])?></small></td>
		<td><?=contractDisplay($d[contract])?></td>
    </tr>
<? } ?>
</table>
<h1 align="center"><?=$used;?> Process Server Binders</h1>
<? include 'footer.php'; ?>