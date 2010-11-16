<?
mysql_connect();
mysql_select_db('core');
function id2name($id){
	$q="SELECT name FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return $d[name];
}
$r=@mysql_query("select * from ps_packets where packet_id = '$_GET[packet]'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$r1=@mysql_query("select ps_plaintiff, full_name from attorneys where attorneys_id = '$d[attorneys_id]'");
$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
$r2=@mysql_query("SELECT * FROM ps_history WHERE packet_id='$_GET[packet]' AND wizard='CERT MAILING' ORDER BY defendant_id ASC");
$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
	$mailing .= $d2[action_str];
	$crr=$d2[action_type];
	$mailerID = $d2[serverID];
}
$date1=date('jS');
$date2=date('F, Y');
$title="CERTIFICATE OF SERVICE";
$text="I hereby certify that a copy of the foregoing document was mailed, first-class, postage prepaid, this $date1 day of $date2, to:<br>";
$court=strtoupper($d[circuit_court]);
if ($d[altPlaintiff] != ''){
	$plaintiff=str_replace("-","<br>",$d[altPlaintiff]);
}else{
	$plaintiff=str_replace("-","<br>",$d1[ps_plaintiff]);
}
$header="<td colspan='2' align='center' style='font-size:20px; line-height:20px;'>CIRCUIT COURT OF $court, MARYLAND</td></tr>
		<tr></tr>
		<tr><td class='a' width='550px'><span style='border-bottom:1px solid black;'>$plaintiff</span><small><br /><em>Plaintiff</em></small><br /><br />v.<br /><br />";
			$header .= strtoupper($d['name1']).'<br>';
			if ($d['name2']){$header .= strtoupper($d['name2']).'<br>';}
			if ($d['name3']){$header .= strtoupper($d['name3']).'<br>';}
			if ($d['name4']){$header .= strtoupper($d['name4']).'<br>';}
			if ($d['name5']){$header .= strtoupper($d['name5']).'<br>';}
			if ($d['name6']){$header .= strtoupper($d['name6']).'<br>';}
			$header .=strtoupper($d['address1']).'<br>';
			$header .="<span style='border-bottom:1px solid black;'>".strtoupper($d['city1']).', '.strtoupper($d['state1']).' '.$d['zip1'].'</span><br>';
			$header .= "<small><em>Defendant</em></small></td>
				<td align='right' valign='top' style='width:100px;' nowrap='nowrap'><div style='font-size:24px; border:solid 1px #666666; text-align:center;'>Case Number<br />".str_replace(0,'&Oslash;',$d[case_no])."</div>";
$cord=$d[packet_id]."-CERT-".$mailerID."%";
?>
<style>
td { font-variant:small-caps; font-size:12px;}
td.a {font-size:14px;}
td.b {font-size:24px; text-decoration:underline;}
table {page-break-after:always;}
</style>
<div style="height:50px"></div>
<center><div style="width: 700px;">
<table align="center" width="100%" border="0">
<tr><?=$header?><IMG SRC='http://staff.mdwestserve.com/barcode.php?barcode=<?=$cord?>&width=350&height=40'><center>File Number: <?=$d[client_file]?><br>Set 1</center></td></tr>
<tr><td colspan='2' class='b' align='center' style='line-height:40px;'><?=$title?></td></tr>
<tr><td colspan='2' style='text-indent:20px; line-height:15px; font-size:14px; padding-bottom:10px;'><?=$text?></td></tr>
<tr><td colspan='2' style="font-weight:bold; padding-left:25px;"><?=$mailing?></td></tr>
<tr><td></td width='50%'><td align='left'><div style='width:250px; height:30px; border-bottom:1px solid black;'>&nbsp;</div>
<div style='padding-top:5px;'><?=id2name($mailerID);?><br>
300 East Joppa Road<br>
Suite 1102<br>
Baltimore, MD 21286</div></td></tr></table></div></center>
<?
$buffer = ob_get_clean();
echo $buffer;
echo str_replace('Set 1','Set 2',$buffer);
?>