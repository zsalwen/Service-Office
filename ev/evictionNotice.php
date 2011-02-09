<?
mysql_connect();
mysql_select_db('service');
function monthConvert($month){
	if ($month == '01'){ return 'January'; }
	if ($month == '02'){ return 'February'; }
	if ($month == '03'){ return 'March'; }
	if ($month == '04'){ return 'April'; }
	if ($month == '05'){ return 'May'; }
	if ($month == '06'){ return 'June'; }
	if ($month == '07'){ return 'July'; }
	if ($month == '08'){ return 'August'; }
	if ($month == '09'){ return 'September'; }
	if ($month == '10'){ return 'October'; }
	if ($month == '11'){ return 'November'; }
	if ($month == '12'){ return 'December'; }
}
function dateExplode($date){
	$date=explode('-',$date);
	$date=monthConvert($date[1])." ".$date[2].", ".$date[0];
	return $date;
}
if ($_GET[mailerID]){
	$mailerID=$_GET[mailerID];
}else{
	$mailerID=$_COOKIE[psdata][user_id];
}
$r=@mysql_query("select * from evictionPackets where eviction_id='$_GET[packet]'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
if (!$_GET[bypass]){
	@mysql_query("INSERT INTO occNotices (requirements, eviction_id, sendDate, clientFile, caseNo, mailerID, county, attorneysID, address, city, state, zip, bill, requestDate ) values ('7-105.9(d)', '$_GET[packet]', NOW(), '$d[client_file]', '$d[case_no]', '$mailerID', '$d[circuit_court]', '$d[attorneys_id]', '$d[address1]', '$d[city1]', '$d[state1]', '$d[zip1]', '5.00', NOW() )");
}
$r1=@mysql_query("select * from attorneys where attorneys_id='$d[attorneys_id]'");
$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
ob_start();
$ratifyDate=strtoupper(dateExplode($d[ratifyDate]));
?>
<style>table {page-break-after:always;}</style>
<table align="center"><tr><td>
<center>
<div align="left" style="width:700px;">
<span style="font-size: 14px; font-weight:bold;">BY FIRST CLASS MAIL</span><br><br>

<span style="font-size: 16px; font-weight:bold;">TO ALL OCCUPANTS</span><br><br>

<center style="font-size: 16px; font-weight:bold;">IMPORTANT EVICTION NOTICE</center><br><br>
<div style="font-size: 16px; text-indent: 30px; font-weight:bold;">
THE CIRCUIT COURT FOR <?=strtoupper($d[circuit_court])?> HAS ENTERED A JUDGMENT AWARDING POSSESSION OF THE PROPERTY LOCATED AT <?=strtoupper($d[address1])?>, <?=strtoupper($d[city1])?>, <?=strtoupper($d[state1])?> <?=strtoupper($d[zip1])?>. YOU COULD BE EVICTED FROM THE PROPERTY ON ANY DAY AFTER <?=$ratifyDate?>.<br><br>
</div>
<div style="font-size: 16px; text-indent: 30px; font-weight:bold;">
BELOW YOU WILL FIND THE NAME, ADDRESS, AND TELEPHONE NUMBER OF THE PERSON WHO PURCHASED THE PROPERTY OR THE PURCHASER'S AGENT. YOU MAY CONTACT THIS PERSON TO FIND OUT MORE ABOUT THE COURT ORDER. FOR FURTHER INFORMATION, YOU MAY REVIEW THE FILE IN THE OFFICE OF THE CLERK OF THE CIRCUIT COURT. YOU MAY WANT TO CONSULT AN ATTORNEY TO DETERMINE YOUR RIGHTS. YOU MAY ALSO CONTACT THE MARYLAND DEPARTMENT OF HOUSING AND COMMUNITY DEVELOPMENT, AT 877-775-0357, OR CONSULT THE DEPARTMENT'S WEBSITE, HTTP://WWW.MDHOPE.ORG, FOR ASSISTANCE. <br><br>
</div>
<div style="font-size: 16px; font-weight:bold;">
PURCHASER OF THE PROPERTY OR PURCHASER'S AGENT:<br><br></div>
<div style="width:450px; border-bottom:solid 1px;"><?=strtoupper($d[movant]);?></div>
NAME<br><br>
<div style="width:450px; border-bottom:solid 1px;"><?=strtoupper(str_replace('-','<br>',$d1[justAddress]));?></div>
ADDRESS<br><br>
<div style="width:450px; border-bottom:solid 1px;"><?=$d1[phone]?></div>
TELEPHONE<br><br>
<div style="width:450px; border-bottom:solid 1px;"><?=date('F jS, Y')?></div>
DATE OF THIS NOTICE<br>
<?
$buffer = ob_get_clean();
echo $buffer;
echo "<br><small>SB842 ($_GET[packet])</small></div></center></td></tr></table>";
echo $buffer;
echo "<br><small>SB842 ($_GET[packet])</small></div></center></td></tr></table>";
echo $buffer;
echo "<br><small>SB842 ($_GET[packet])</small></div></center></td></tr></table>";
?>