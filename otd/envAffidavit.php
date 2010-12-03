<?
mysql_connect();
mysql_select_db('core');
function id2name($id){
	$q="SELECT name FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[name];
}
function county2envelope2($county){
	$county=strtoupper($county);
	if ($county == 'BALTIMORE'){
		$search='BALTIMORE COUNTY';
	}elseif($county == 'PRINCE GEORGES'){
		$search='PRINCE GEORGE';
	}elseif($county == 'ST MARYS'){
		$search='ST. MARY';
	}elseif($county == 'QUEEN ANNES'){
		$search='QUEEN ANNE';
	}else{
		$search=$county;
	}
	if ($county == 'BALTIMORE CITY'){
		return "Clerk of Court, 460 Courthouse East, 111 N. Calvert Street, Baltimore, MD 21202";
	}else{
		$r=@mysql_query("SELECT to1, to2, to3 FROM envelopeImage WHERE to1 LIKE '%$search%' AND addressType='COURT'");
		$d=mysql_fetch_array($r,MYSQL_ASSOC);
		return $d[to1].', '.$d[to2].', '.$d[to3];
	}
}
$r=@mysql_query("select * from ps_packets where packet_id = '$_GET[packet]'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$r1=@mysql_query("select ps_plaintiff, full_name from attorneys where attorneys_id = '$d[attorneys_id]'");
$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
ob_start();
$court=strtoupper($d[circuit_court]);
$county=county2envelope2($d[circuit_court]);
if ($d[altPlaintiff] != ''){
	$plaintiff=str_replace("-","<br>",$d[altPlaintiff]);
}else{
	$plaintiff=str_replace("-","<br>",$d1[ps_plaintiff]);
}
$header="<td class='a' width='550px'>$plaintiff<br><small>_________________________<br /><em>Substitute Trustees<br>Plaintiff(s)</em></small><br /><br />v.<br /><br />";
			$header .= strtoupper($d['name1']).'<br>';
			if ($d['name2']){$header .= strtoupper($d['name2']).'<br>';}
			if ($d['name3']){$header .= strtoupper($d['name3']).'<br>';}
			if ($d['name4']){$header .= strtoupper($d['name4']).'<br>';}
			if ($d['name5']){$header .= strtoupper($d['name5']).'<br>';}
			if ($d['name6']){$header .= strtoupper($d['name6']).'<br>';}
			$header .=strtoupper($d['address1']).'<br>';
			$header .=strtoupper($d['city1']).', '.strtoupper($d['state1']).' '.$d['zip1'].'<br>';
			$header .= "<small>_________________________<br /><em>Defendant(s)</em></small></td><td style='width:10px;'>**************</td>
				<td align='right' valign='top' style='width:100px;' nowrap='nowrap'><div style='font-size:16px; border:solid 1px #666666; text-align:center;'>IN THE<BR>CIRCUIT COURT<BR>FOR<BR>$court<BR><BR>Case N&ordm; ".str_replace(0,'&Oslash;',$d[case_no])."</div>";
$mailerID=$_COOKIE[psdata][user_id];
$cord=$d[packet_id]."-ENV-".$mailerID."%";
$mailer=$_COOKIE[psdata][name];
//determine Employee Title
if ($mailerID == 1){
	$position="Operations Manager";
}elseif($mailerID == 2){
	$position="Service Manager";
}elseif($mailerID == 3){
	$position="Vice President";
}else{
	$position="Document Processor";
}
//determine whether pd or m&p, find date, append correct terminology
//$sent=" via personal delivery";
//$sent=" via posting to the property";
//$sent=" via the United States Postal Service by Certified Mail, Return Receipt Requested, and by First Class Mail";
if ($d[lossMit] == 'PRELIMINARY'){
	$title="AFFIDAVIT OF INCLUSION OF ENVELOPE PURSUANT TO MD. REAL<br>PROPERTY CODE ARTICLE &#167;7-105.1(d)(2)(viii)(2)(D)(re: PLMA)";
	$text="Pursuant to Maryland Real Property Code Article &#167;7-105.1(d)(2)(viii)(2)(D), I hereby certify that on [DATE], an envelope addressed to the attorney handling this foreclosure, was sent to the mortgagor(s)/grantor(s)$sent.";
}else{
	$title="AFFIDAVIT OF INCLUSION OF ENVELOPE<br>PURSUANT TO MD. REAL PROPERTY CODE<br>ARTICLE &#167;7-105-1(d)(2)(x)(2) and (3)(re: FLMA - Owner-Occupied)";
	$text="Pursuant to Maryland Real Property Code Article &#167;7-105.1(d)(2)(x)(2) and (3), I hereby certify that on [DATE], two envelopes, including an envelope addressed to the \"$county,\" and an envelope addressed to the attorney handling this foreclosure, were sent to the mortgagor(s)/grantor(s)$sent.";
}

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
<td align="center" colspan="3" valign="top"><span style="font-size:24px; text-decoration:underline;"><?=$title?></span></td>
</tr><tr>
<td colspan="3" style="text-indent:20px"><?=$text?></td>
</tr><tr><td colspan="3" style="text-indent:20px;">I solemnly affirm under the penalties of perjury that the contents of the foregoing paper are true to the best of my knowledge, information, and belief.</td></tr><tr><td></td><td><br>
________________<br>Date
</td><td></td><td><br><span style='text-decoration:underline; width:200px;'>&nbsp;</span><br>Signature<br><span style='text-decoration:underline; width:200px;'><?=$mailer?></span><br>Name [typed]<br>span style='text-decoration:underline; width:200px;'><?=$position?></span><br>Title [typed]<br><span style='text-decoration:underline; width:200px;'>300 E Joppa Road, Suite 1102<br>Towson, MD 21286</span><br>Address of Affiant [typed]</td></tr></table>
</div></center>
<?
$buffer = ob_get_clean();
echo $buffer;
echo str_replace('Set 1','Set 2',$buffer);
?>