<?
$i=0;
while ($i < 6){$i++;
	if ($_POST["serveA$i"]){
		$serveA[$i]=$_POST["serveA$i"];
	}else{
		$serveA[$i]='';
	}
}
if ($ddr == ''){
	$q="INSERT INTO ps_instructions (packetID,attempts,allowPosting,postSeparateDay,allowSubService,photograph,affidavitTemplate,help,serveA1,serveA2,serveA3,serveA4,serveA5,serveA6) values ('$packet','$_POST[attempts]','$_POST[allowPosting]','$_POST[postSeparateDay]','$_POST[allowSubService]','$_POST[photograph]','$_POST[affidavitTemplate]','$_POST[help]','$serveA[1]','$serveA[2]','$serveA[3]','$serveA[4]','$serveA[5]','$serveA[6]')";
}else{
	$q="UPDATE ps_instructions SET attempts='$_POST[attempts]', allowPosting='$_POST[allowPosting]', postSeparateDay='$_POST[postSeparateDay]', allowSubService='$_POST[allowSubService]', photograph='$_POST[photograph]', affidavitTemplate='$_POST[affidavitTemplate]', help='$_POST[help]', serveA1='$serveA[1]', serveA2='$serveA[2]', serveA3='$serveA[3]', serveA4='$serveA[4]', serveA5='$serveA[5]', serveA6='$serveA[6]' WHERE packetID='$packet'";
}
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
echo "<h1>SUCCESSFUL</h1><br><a href='http://service.mdwestserve.com/customInstructions.php?packet=$packet'>BACK TO PREVIEW</a>";
?>