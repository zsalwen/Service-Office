<?
mysql_connect();
mysql_select_db('core');
$r=@mysql_query("select * from ps_packets where packet_id = '$_GET[packet]'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$r1=@mysql_query("select ps_plaintiff, full_name from attorneys where attorneys_id = '$d[attorneys_id]'");
$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
$date=date('j day of F, Y');
$title="CERTIFICATE OF SERVICE";
$text="I hereby certify that a copy of the foregoing document was mailed, first-class, postage prepaid, this $date, to:<br>";
$court=strtoupper($d[circuit_court]);
if ($d[altPlaintiff] != ''){
	$plaintiff=str_replace("-","<br>",$d[altPlaintiff]);
}else{
	$plaintiff=str_replace("-","<br>",$d1[ps_plaintiff]);
}
$header="<td colspan='2' align='center' style='font-size:20px;'>CIRCUIT COURT OF $court, MARYLAND</td></tr>
		<tr></tr>
		<tr><td class='a' width='550px'>$plaintiff<br><small>_________________________<br /><em>Plaintiff</em></small><br /><br />v.<br /><br />";
			$header .= strtoupper($d['name1']).'<br>';
			if ($d['name2']){$header .= strtoupper($d['name2']).'<br>';}
			if ($d['name3']){$header .= strtoupper($d['name3']).'<br>';}
			if ($d['name4']){$header .= strtoupper($d['name4']).'<br>';}
			if ($d['name5']){$header .= strtoupper($d['name5']).'<br>';}
			if ($d['name6']){$header .= strtoupper($d['name6']).'<br>';}
			$header .=strtoupper($d['address1']).'<br>';
			$header .=strtoupper($d['city1']).', '.strtoupper($d['state1']).' '.$d['zip1'].'<br>';
			$header .= "<small>_________________________<br /><em>Defendant</em></small></td>
				<td align='right' valign='top' style='width:100px;' nowrap='nowrap'><div style='font-size:24px; border:solid 1px #666666; text-align:center;'>Case Number<br />".str_replace(0,'&Oslash;',$d[case_no])."</div>";
$cord=$d[packet_id]."-CERT-".$d2[mailerID]."%";
?>
<style>
td { font-variant:small-caps; font-size:12px;}
td.a {font-size:12px;}
td.b {font-size:24px; text-decoration:underline;}
table {page-break-after:always;}
</style>
<center><div style="width: 600px;">
<table align="center" width="100%" border="0">
<tr><?=$header?><IMG SRC='http://staff.mdwestserve.com/barcode.php?barcode=<?=$cord?>&width=350&height=40'><center>File Number: <?=$d[client_file]?><br>Set 1</center></td></tr>
<tr><td colspan='2' class='b'><?=$title?></td></tr>
<tr><tr colspan='2'><?=$text?></td></tr>