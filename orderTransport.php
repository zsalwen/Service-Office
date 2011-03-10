<?
mysql_connect();
mysql_select_db('core');
function id2attorney($id){
	$q="SELECT display_name FROM attorneys WHERE attorneys_id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[display_name];
}
?>
		<center>UPLOAD FILES TO AUTOSTART, USING NAMING CONVENTION "FILENUMBER_SERVICETYPE.PDF" (E.G. "08-12345_PRESALE.PDF").<BR>ENTER COMMON INSTRUCTIONS BELOW, THEY WILL BE APPLIED TO ALL FILES UPLOADED SIMULTANEOUSLY.<BR>BE SURE TO SELECT THE PROPER ATTORNEY <i>AND</i> SERVICE TYPE.</center>
		<form action="http://staff.mdwestserve.com/orderUpload.php" method="post">
		<input type="hidden" name="uploadEmail" value="<?=$_COOKIE[psdata][email]?>">
		<textarea name="attorneyNotes" rows="4" cols="50"></textarea><br>
		<select name="attorneysID">
		<option value="">SELECT ATTORNEY</option>
		<?
		$q="SELECT DISTINCT attorneys_id FROM ps_packets";
		$r=@mysql_query($q) or die(mysql_error());
		while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
			echo "<option value='".$d[attorneys_id]."'>".id2attorney($d[attorneys_id])."</option>";
		}
		?>
		</select><select name="svcType"><option>OTD</option><option>MAIL ONLY</option><option>EV</option><option>S</option></select><input type="submit" name="submit" value="Process Orders"></form>