<?
mysql_connect();
mysql_select_db('service');
$def=$_GET[def];
$packet=$_GET[packet];
$packet2="S".$_GET[packet];
$q1="SELECT * from standard_packets WHERE packet_id='$packet'";
$r1=@mysql_query($q1) or die(mysql_error());
$d1=mysql_fetch_array($r1, MYSQL_ASSOC);
if ($d1[onAffidavit1]=='checked'){$header .= strtoupper($d1['name1']).'<br>';}
if ($d1['name2'] && $d1[onAffidavit2]=='checked'){$header .= strtoupper($d1['name2']).'<br>';}
if ($d1['name3'] && $d1[onAffidavit3]=='checked'){$header .= strtoupper($d1['name3']).'<br>';}
if ($d1['name4'] && $d1[onAffidavit4]=='checked'){$header .= strtoupper($d1['name4']).'<br>';}
if ($d1['name5'] && $d1[onAffidavit5]=='checked'){$header .= strtoupper($d1['name5']).'<br>';}
if ($d1['name6'] && $d1[onAffidavit6]=='checked'){$header .= strtoupper($d1['name6']).'<br>';}
$q2="SELECT * from attorneys where attorneys_id = '$d1[attorneys_id]'";
$r2=@mysql_query($q2) or die(mysql_error());
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
if ($d1[altPlaintiff] != '' && $d1[attorneys_id] != '1'){
	$plaintiff = str_replace('-','<br>',$d1[altPlaintiff]);
}elseif($d1[altPlaintiff] != ''){
	$plaintiff = str_replace('-','<br>',$d1[altPlaintiff]);
}else{
	$plaintiff = str_replace('-','<br>',$d2[ps_plaintiff]);
}
if ($d1[case_no]){
	$case_no=$d1[case_no];
}else{
	$case_no="<i>UNAVAILABLE</i>";
}
$deadline=strtotime($d1[date_received]);
$received=date('m/d/Y',$deadline);
$deadline=$deadline+432000;
$deadline=date('m/d/Y',$deadline);
//http://mdwestserve.com/images/banners/logo.jpg
?>
<style>
body, table {font-weight:bold; padding:0px; font-size:16px;}
td { font-variant: small-caps;  font-size:13px;}
</style>
<!--------
<img style="position:absolute; left:0px; top:0px; width:160px; height:160px;" src="http://service.mdwestserve.com/smallLogo.jpg" class="logo">
------------->
<table width="600px" align="center" <? if ($_GET[pageBreak]){ echo "style='page-break-before:always;'"; }?>>
	<tr>
		<td  valign="bottom" align="center" height="50px;"><div style="font-size:30px; font-variant:small-caps;">MDWestServe, Inc.</div>300 East Joppa Road, Suite 1103<br>Towson, MD 21286<br>(410) 769-9797</td>
	</tr>
	<tr>
		<td  align="center" style="font-size:22px; font-variant:small-caps;" height="50px">Field Sheet - <?=$packet2?>: <?=$d1["name$def"]?></td>
	</tr>
	<tr>
	<td><table style="font-size:14px;" width="100%">
		<tr>
			<td style="border-bottom:solid 1px; width:350px;" colspan="2">Case # <?=strtoupper($case_no);?></td>
			<td rowspan="3" align="right" valign="middle"><div style="border:3px double; width: 250px; padding-left: 10px;" align="left"><?=strtoupper($plaintiff)?><br>v.<br><?=$header?></div></td>
		</tr>
		<tr>
			<td style="border-bottom:solid 1px; width:300px;" colspan="2">File Deadline: <?=$deadline?></td>
		</tr>
		<tr>
			<td style="border-bottom:solid 1px; width:300px;" colspan="2">Date Served:</td>
		</tr>
		<tr>
			<td style="border-bottom:solid 1px; width:300px">Time:</td>
			<td align="right">AM/PM</td>
		</tr>
	</table></td>
	</tr>
	<tr>
		<td  style="border-bottom:solid 1px; width: 300px;">Documents Served: <small><?=strtoupper($d1["addlDocs"]);?></small></td>
	</tr>
	<tr>
		<td  style="border-bottom:solid 1px; width: 300px;">Name of Person Served:</td>
	</tr>
	<tr>
		<td  style="border-bottom:solid 1px; width: 300px;">Relationship To <?=strtoupper($d1["name$def"]);?>:</td>
	</tr>
	<tr>
		<td  style="border-bottom:solid 1px; width: 300px;">Address Served:</td>
	</tr>
	<tr>
		<td><table align="center" width="100%" style="font-size:14px;"><tr><td style="border-bottom:solid 1px; width: 70px;">Age:</td>
		<td>Sex: M F</td>
		<td style="border-bottom:solid 1px; width: 70px;">Race:</td>
		<td style="border-bottom:solid 1px; width: 70px;">Height:</td>
		<td style="border-bottom:solid 1px; width: 70px;">Weight:</td>
		<td style="border-bottom:solid 1px; width: 70px;">Hair:</td>
		<td>Beard: Y N</td>
		<td>Glasses: Y N</td></tr></table></td>
	</tr>
	<tr>
		<td >Attempts:</td>
	</tr>
	<tr><td><table align="center" width="100%" border="1" style="border-collapse:collapse;"><tr>
		<td style="border-bottom:solid 2px; width: 100px;" align="center">Date:</td>
		<td style="border-bottom:solid 2px; width: 100px;" align="center">Time:</td>
		<td style="border-bottom:solid 2px; width: 400px;" align="center">Result/Person Spoken To:</td>
	</tr><tr>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 400px; height:25px;">&nbsp; </td>
	</tr><tr>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 400px; height:18px;">&nbsp; </td>
	</tr><tr>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 400px; height:18px;">&nbsp; </td>
	</tr><tr>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 400px; height:18px;">&nbsp; </td>
	</tr><tr>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 400px; height:18px;">&nbsp; </td>
	</tr><tr>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 400px; height:18px;">&nbsp; </td>
	</tr><tr>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 400px; height:18px;">&nbsp; </td>
	</tr><tr>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 400px; height:18px;">&nbsp; </td>
	</tr><tr>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 400px; height:18px;">&nbsp; </td>
	</tr><tr>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 100px; height:25px;">&nbsp; </td>
		<td style="border-bottom:solid 2px; width: 400px; height:18px;">&nbsp; </td>
	</tr></table></td></tr>
	<tr>
		<td style="font-size:16px; height:80px;">I acknowledge receipt of the documents listed above.  I am of legal age and confirm that the above address is the usual place of abode or employment for the above named Defendant.</td>
	</tr>
	<tr><td><table><tr>
		<td style="border-top:solid 1px; font-size:16px; width:400px">Signature of Recipient [optional]</td>
		<td style="border-top:solid 1px; font-size:16px; width:200px;">Date</td>
	</tr>
	<!-----
	<tr>
		<td><img style="position:relative; bottom:0px;" src="http://mdwestserve.com/images/banners/logo.jpg"></td>
	</tr>
	------------>
</table>
	</tr>
</table>
<?
if ($_GET['autoPrint'] == 1){
echo "<script>
if (window.self) window.print();
self.close();
</script>";
}
?>