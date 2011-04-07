<?
mysql_connect();
mysql_select_db('core');
if ($_GET[mailerID]){
	$mailerID=$_GET[mailerID];
}else{
	$mailerID=$_COOKIE[psdata][user_id];
}
$r=@mysql_query("select * from ps_packets where packet_id='$_GET[packet]'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$address=strtoupper("$d[address1], $d[city1], $d[state1] $d[zip1]");
if (!$_GET[bypass]){
	@mysql_query("INSERT INTO occNotices (requirements, packet_id, sendDate, clientFile, caseNo, mailerID, county, attorneysID, address, city, state, zip, bill, requestDate ) values ('7-105.9(b)', '$_GET[packet]', NOW(), '$d[client_file]', '$d[case_no]', '$mailerID', '$d[circuit_court]', '$d[attorneys_id]', '$d[address1]', '$d[city1]', '$d[state1]', '$d[zip1]', '5.00', NOW() )");
}
if ($_GET[bypass]){
	$r2=@mysql_query("SELECT * from occNotices where packet_id='$_GET[packet]'");
	$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
	$today=date('F jS, Y',strtotime($d2[sendDate]));
	$address=strtoupper("$d[address], $d[city], $d[state] $d[zip]");
}else{
	$today=date('F jS, Y');
}
$r1=@mysql_query("select * from attorneys where attorneys_id='$d[attorneys_id]'");
$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
ob_start();
?>
<style>table {page-break-after:always;}</style>
<table align="center"><tr><td><div align="left" style="width:700px;">
<span style="font-size: 14px; font-weight:bold;">BY FIRST CLASS MAIL</span><br><br>

<span style="font-size: 14px; font-weight:bold;">TO ALL OCCUPANTS</span><br><br>

<center style="font-size: 14px; font-weight:bold;">IMPORTANT NOTICE</center><br><br>
<div style="font-size: 14px; text-indent: 30px; font-weight:bold;">
A FORECLOSURE ACTION HAS BEEN FILED AGAINST THE PROPERTY LOCATED AT
<?=$address?> IN THE CIRCUIT COURT FOR <?=strtoupper($d[circuit_court])?>. THIS NOTICE IS BEING SENT TO YOU AS A PERSON WHO LIVES IN THIS PROPERTY.<br><br>
</div>
<div style="font-size: 14px; text-indent: 30px; font-weight:bold;">
A FORECLOSURE SALE OF THE PROPERTY MAY OCCUR AT ANY TIME AFTER 45
DAYS FROM THE DATE OF THIS NOTICE.<br><br>
</div>
<div style="font-size: 14px; text-indent: 30px; font-weight:bold;">
MOST RENTERS HAVE THE RIGHT TO CONTINUE RENTING THE PROPERTY AFTER IT IS SOLD AT FORECLOSURE.  THE FORECLOSURE SALE PURCHASER BECOMES THE NEW LANDLORD.<BR><BR>
</div>
<div style="font-size: 14px; text-indent: 30px; font-weight:bold;">
MOST RENTERS WITH A LEASE FOR A SPECIFIC PERIOD OF TIME HAVE THE RIGHT TO CONTINUE RENTING THE PROPERTY UNTIL THE END OF THE LEASE TERM.  MOST MONTH-TO-MONTH RENTERS HAVE THE RIGHT TO CONTINUE RENTING THE PROPERTY FOR 90 DAYS AFTER RECEIVING A WRITTEN NOTICE TO VACATE FROM THE NEW OWNER.<BR><BR>
</div>
<div style="font-size: 14px; text-indent: 30px; font-weight:bold;">
YOU SHOULD GET LEGAL ADVICE TO DETERMINE IF YOU HAVE THESE RIGHTS.<BR><BR>
</div>
<div style="font-size: 14px; text-indent: 30px;">
Below you will find the name, address, and telephone number of the person authorized to sell the property.  You may contact this person to <b>NOTIFY HIM OR HER THAT YOU ARE A TENANT AT THE PROPERTY AND TO</b> find out more about the sale.  For further information, you may review the file in the office of the clerk of the circuit court. You also may contact the Maryland Department of Housing and community Development, at 1-877-462-7555, or consult the Department's website, http://www.mdhope.org/, for assistance.<br><br>
</div>
<div style="font-size: 14px; font-weight:bold;">
PERSON AUTHORIZED TO SELL THE PROPERTY:<br><br>

<div style="width:450px; border-bottom:solid 1px;"><?=str_replace('-','<br>',$d1[authSeller])?>, Substitute Trustee(s)</div>
NAME<br><br>
<div style="width:450px; border-bottom:solid 1px;"><?=str_replace('-','<br>',$d1[address])?></div>
ADDRESS<br><br>
<div style="width:450px; border-bottom:solid 1px;"><?=$d1[phone]?></div>
TELEPHONE<br><br>
<div style="width:450px; border-bottom:solid 1px;"><?=$today?></div>
DATE OF THIS NOTICE<br>
</div>
<div style="font-size: 14px;">
<center style="font-weight:bold;text-decoration:underline;">NOTICE</center>
PURSUANT TO THE FEDERAL FAIR DEBT COLLECTION PRACTICES ACT, WE ADVISE YOU THAT THIS FIRM IS A DEBT COLLECTOR ATTEMPTING TO COLLECT THE INDEBTEDNESS REFERRED TO HEREIN AND ANY INFORMATION WE OBTAIN FROM YOU WILL BE USED FOR THAT PURPOSE.  IN THE EVENT YOU ARE NOW IN A BANKRUPTCY PROCEEDING, HAVE OBTAINED A DISCHARGE IN BANKRUPTCY OR HAVE OTHERWISE BEEN RELEASED FROM PERSONAL LIABILTY, THIS NOTICE (AND OUR COLLECTION EFFORTS) MAY ONLY AFFECT YOUR OWNERSHIP OR POSSESSORY INTEREST IN THE SUBJECT PROPERTY AND NOT YOUR PERSONAL OBLIGATIONS UNDER THE MORTGAGE DOCUMENTS.

</div>
<?
$buffer = ob_get_clean();
echo $buffer;
echo "<br><small>SB842 (OTD$_GET[packet])</small></td></tr></table>";
echo $buffer;
echo "<br><small>SB842 (OTD$_GET[packet])</small></td></tr></table>";
echo $buffer;
echo "<br><small>SB842 (OTD$_GET[packet])</small></td></tr></table>";
?>