<?
if ($_COOKIE[psdata][level] != "Operations"){
	header('Location: http://service.mdwestserve.com');
}
include 'common.php';
hardLog('Service Lookup','user');
function payweeks(){
	$today = date('Y-m-d');
	$q="SELECT * FROM paychecks";
	$r=@mysql_query($q);
	while ($d = mysql_fetch_array($r, MYSQL_ASSOC)){
		if ($today >= $d[period_start] && $today <= $d[period_end]){
			$option .= "<option selected value='$d[period_start]'>$d[period_start] to $d[period_end]</option>";
		}else{
			$option .= "<option value='$d[period_start]'>$d[period_start] to $d[period_end]</option>";
		}
	}
	return $option;
}
?>
<table>
	<tr>
		<td valign='top'>
<div><b>User Tools</b></div> 
		<ol>
    <form action="http://staff.mdwestserve.com/print_timeclock.php" target="_blank">
	<li><select name="start"><?=payweeks();?></select><input type="submit" value="View Time Card" /></li>
	</form>
		</ol>
<div><b>Presale Processing</b></div>
<ol>
		<form action="http://staff.mdwestserve.com/otd/details.php">
    <li class="monitor">Load Details <input size="4" name="packet" value="packet" onclick="value=''"/></li>
        </form>

	<form action="http://staff.mdwestserve.com/otd/occAffidavit.php"><li class="push">Generate Occupant Affidavit <input name="packet" value="packet" onclick="value=''" size="4"></li></form>
	    <form action="http://service.mdwestserve.com/wizard.php">
     <li class="push">Load Wizard: <input name="jump" value="jump" onclick="value=''" size="4" /><input name="server" value="server" onclick="value=''" size="4" /> <input type="submit" value="." /></li>
        </form>
		<form action="http://service.mdwestserve.com/liveAffidavit.php">
     <li class="push">Print Run <input name="server" value="server" onclick="value=''" size="3" /><input name="start" value="start" onclick="value=''" size="2" /><input name="stop" value="stop" onclick="value=''" size="2"/><input type="submit" value="!" /></li>
        </form>
		
		<form action="http://staff.mdwestserve.com/textGen.php">
	<li class="push">Generate Out-of-State Service Email: <input name="packet" value="packet" onclick="value=''" size="4"></li></form>
</ol>

<div><b>Eviction Processing</b></div>
	<ol>		
		<form action="http://staff.mdwestserve.com/ev/details.php">
    <li class="monitor">Load Details <input size="4" name="packet" value="packet" onclick="value=''"/></li>
        </form>
	
	<form action="http://service.mdwestserve.com/ev_wizard.php">
     <li class="push">Load Wizard: <input name="jump" value="jump" onclick="value=''" size="4" /><input name="server" value="server" onclick="value=''" size="4" /> <input type="submit" value="." /></li>
    </form>
	<li class="afftrack"><a href="http://staff.mdwestserve.com/ev/docuTrack.php" target='_Blank'>Document Tracker*</a></li>
	
	
	</ol>
<div><b>Standard Processing</b></div>
	<ol>		
		<form action="http://staff.mdwestserve.com/standard/order.php">
    <li class="monitor">Load Order <input size="4" name="packet" value="packet" onclick="value=''"/></li>
        </form>
	
	<form action="http://staff.mdwestserve.com/service/wizard.php">
     <li class="push">Load Wizard: <input name="packet" value="packet" onclick="value=''" size="4" /><input name="server" value="server" onclick="value=''" size="4" /> <input type="submit" value="Affidavits" /></li>
    </form>
	<li class="afftrack"><a href="http://staff.mdwestserve.com/ev/docuTrack.php" target='_Blank'>Document Tracker*</a></li>
	
	
	</ol>
		</td>
<td valign="top">
	<div><b>Combined Processes</b></div>
	<ol>
	
	<form action="http://service.mdwestserve.com/ps_worksheet.php" target="_blank">
	<li class="monitor"><select name="status" style="font-size:11px;"><option value="">Select Server Worksheet</option>
		<?
		$q7= "select * from ps_users WHERE contract = 'YES' order by id ASC";
		$r7=@mysql_query($q7) or die("Query: $q7<br>".mysql_error());
		$i2=0;
		while ($d7=mysql_fetch_array($r7, MYSQL_ASSOC)) {$i2++;
		?>
		<option value="<?=$d7[id]?>" style="background-color:<?=row_color($i2,'#FFCCCC','#cccccc');?>"><? if ($d7[company]){echo $d7[company].', '.$d7[name] ;}else{echo $d7[name] ;}?></option>
		<?        } ?>
	</select><input type="submit" value="GO"></li></form>
	<form action="http://staff.mdwestserve.com/mailerSwap.php">
    <li class="mail">Mailer Swap <input name="packet" value="packet" onclick="value=''" size="4" /> OTD<input type="radio" name="svc" value="OTD" checked="yes"> EV<input type="radio" name="svc" value="EV"> <input type="submit" value="!" /></li>
	</form>
	</ol>
	<div id="search" align="center" style="padding:0px;margin:0px">
	<form action="http://staff.mdwestserve.com/search.php" method="get" target="_blank" style="padding:0px;margin:0px">
	<input name="q" /><select name="field">
			<option value="client_file">File</option>
			<option value="packet_id">Packet</option>
			<option value="eviction_id">Eviction</option>
			<option value="case_no">Case</option>
			<option value="name">Name</option>
			<option value="address">Address</option>
		</select><input type="submit" value="Search" name="search" />
	</form>
	</div>
	</td>
	</tr>
</table>
