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
$r2=@mysql_query("select * from occNotices where packet_id = '$_GET[packet]'");
$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
$sendDate=explode('-',$d2[sendDate]);
$sendDate=$sendDate[1]."/".$sendDate[2]."/".$sendDate[0];
ob_start();
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
$cord=$d[packet_id]."-OCC-".$d2[mailerID]."%";
$mailer=id2name($d2[mailerID]);
?>
<style>
td { font-variant:small-caps; font-size:12px;}
td.a {font-size:12px;}
td.b {font-size:24px;}
table {page-break-after:always;}
</style>
<div style="height:50px"></div>
<center><div style="width: 600px;">
<table align="center" width="100%" border="0">
<tr><?=$header?><IMG SRC='http://staff.mdwestserve.com/barcode.php?barcode=<?=$cord?>&width=350&height=40'><center>File Number: <?=$d[client_file]?><br>Set 1</center></td>
</tr><tr>
<td align="center" colspan="2" valign="top"><span style="font-size:24px;">AFFIDAVIT OF MAILING</span><br>
"Notice to Occupant by First-Class Mail"</td>
</tr><tr>
<td colspan="2" style="text-indent:40px">I hereby certify in compliance with 7-105.9(b) "WRITTEN NOTICE ADDRESSED TO 'ALL OCCUPANTS'" that I mailed to the occupant, in compliance with 7-105.9(b)(2)(iii)
"SENT BY FIRST-CLASS MAIL" by U.S. First Class Mail, postage prepaid.<br><br></td>
</tr><tr>
<td colspan="2" style="text-indent:40px;">This Notice was sent to the following persons at the following addresses on the
following respective dates:<br><br></td>
</tr><tr>
<td colspan="2" style="font-weight:bold; padding-left:40px;">
ALL OCCUPANTS<br>
<?=strtoupper($d[address1]);?><br>
<?=strtoupper($d[city1]).', '.strtoupper($d[state1]).' '.$d[zip1];?><br>
MAILED <?=$sendDate?><br><br>
</td></tr><tr><td colspan="2" style="text-indent:40px;">
I solemnly affirm under the penalty of perjury to the best of my knowledge, information
& belief that the contents of the foregoing paper are true and correct.
</td></tr><tr><td></td><td><br>
_______________________________________<u>DATE:</u>________
<div style="font-size:12px;"><?=$mailer?><br>
MDWestServe, Inc. for <?=$d1[full_name]?><br>
300 East Joppa Road<br>
Suite 1103<br>
Baltimore, MD 21286</div>
</td><td></td></tr></table>
</div></center>
<?
$buffer = ob_get_clean();
echo $buffer;
echo str_replace('Set 1','Set 2',$buffer);
?>