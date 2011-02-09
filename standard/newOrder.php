<?
mysql_connect();
mysql_select_db('service');
?>
<style>
body {margin:0px; padding:0px;}
a {text-decoration:none; color:#000099; font-weight:bold;}
a:hover {text-decoration:none; color:#000000; font-weight:bold;}
</style>
<style type="text/css">
    @media print {
      .noprint { display: none; }
    }
  </style> 
<table align="center" cellpadding="0" cellspacing="0"><tr><td valign="top"  bgcolor="#FFFFFF">

<?

if ($_POST[attid] && $_POST[client_file]){//c1
	@mysql_query("insert into ps_file_array (name, type, size, tmp_name, error, uploadDate) values ('".$_FILES['otd']['name']."','".$_FILES['otd']['type']."','".$_FILES['otd']['size']."','".$_FILES['otd']['tmp_name']."','".$_FILES['otd']['error']."', NOW() )") or die(mysql_error());
	// check file size and error out
	//$print = lpwords('OTD','0',' ');
	$print .= date("F d Y H:i:s.")." : Portal upload started\n";

	if ($_FILES['otd']['size'] < 188743680){//c3
		if ($_FILES['otd']['size'] == 0 && $_FILES['otd']['tmp_name']){//c4
			$error = "<div>Upload Failed : Browser Error (Contact your IT Department)</div>";
			$print .= date("F d Y H:i:s.")." : Upload Failed : Browser Error\n";
			//mail('sysop@hwestauctions.com','failed upload 0 size',$_COOKIE[psdata][user_id]);
		}else{//c4
			// ok first we need to go get the files
			$html = "<hr>";
			//echo "<h2 align='center'>Transmitting $_POST[file] :: $_POST[last_fault]<br> ".$user[name]." ($ip)</h2>";
			$path = "/data/service/orders/";
			if (!file_exists($path)){//c5
				mkdir ($path,0777);
			}//c5
			$html .= "<h1>Standard Process Service Confirmation</h1>";
			$html .= "<h2>Recieved: ".date('r')."</h2>";
			$html .= "<h2>Case: $_POST[case_no]</h2>";
			$html .= "<h2>File: $_POST[client_file]</h2>";
			$html .= "<h2>Size: ".$_FILES['otd']['size']."</h2>";
			$print .= date("F d Y H:i:s.")." : Case Number $_POST[case_no]\n";
			$print .= date("F d Y H:i:s.")." : File Number $_POST[client_file]\n";
			$print .= date("F d Y H:i:s.")." : File Size ".$_FILES['otd']['size']."\n";
			$file_path = $path.$_POST[client_file]."-".date('r');
			if (!file_exists($file_path)){//c6
				mkdir ($file_path,0777);
			}//c6
			$target_path = $file_path."/". basename( $_FILES['otd']['name']); 
			//echo "$target_path<br>";
			if(move_uploaded_file($_FILES['otd']['tmp_name'], $target_path)) {//c7
				$html .= "<h3>Standard Process Service Order '".  basename( $_FILES['otd']['name'])."' has been recieved.</h2>";
				$print .= date("F d Y H:i:s.").": $target_path has been recieved. \n";
				$print .= date("F d Y H:i:s.").": Client Note: $_POST[attorney_notes] \n";
			}//c7
			$link1 = "$target_path"; 
			$notes = $_POST[attorney_notes];
			$attorney_notes = addslashes($notes);
			$timeline=date("m/d/y H:i:s A")." File Sent Through Client Portal By ".$user[name];
			if ($_POST[new_affidavit_status]){
				$jobType = $_POST[new_affidavit_status];
			}else{
				$jobType = $_POST[affidavit_status];
			}
			if ($_POST[new_addlDocs]){
				$jobPapers = $_POST[new_addlDocs];
			}else{
				$jobPapers = $_POST[addlDocs];
			}
			$query = "INSERT INTO standard_packets (date_received, process_status, addlDocs, affidavit_status, case_no, otd, attorneys_id, contact, ip, status, attorney_notes, client_file, timeline) values (NOW(), 'IN PROGRESS', '$jobPapers', '$jobType', '$_POST[case_no]', '$link1', '$_POST[attid]', '$id', '$ip', 'NEW', '$attorney_notes', '$_POST[client_file]', '$timeline')";
			@mysql_query($query) or die(mysql_error());
			//echo "$query ".mysql_error();
			$print .= date("F d Y H:i:s.")." : Uploaded by ".$user[name]." \n";
			$print .= date("F d Y H:i:s.")." : New Packet ID ".mysql_insert_id()." \n";
			$print .= date("F d Y H:i:s.")." : Portal upload complete\n";
			$html .= "<h2>New Packet ID ".mysql_insert_id()."</h2>";
			echo $html;
			//echo $print;
			mail('service@mdwestserve.com',$_COOKIE[psdata][name].': NEW STANDARD SERVICE ORDER FOR '.$_POST[client_file],addslashes($print.$_POST[attorney_notes]));
			portal_log("Sent Standard Process Service Packet for $_POST[case_no]", $user[contact_id]);
		}//c4
	}else{//c3
		$error = "<div>Your file size was to large, contact 410-828-4568 for assistance.</div>";
		mail('service@mdwestserve.com','failed upload too large',$_COOKIE[psdata][user_id]);
	}//c3
}//c1




if ($error){
?>
<div align="center" style="font-size:22px; border:double; padding:5px; background-color:#FF0000 "><?=$error?></div>
<? } ?>









<? if($_FILES['otd']['error'] && $_FILES['otd']['error'] != 0){?>
<div align="center" style="font-size:22px; border:double; padding:5px; background-color:#FF0000 ">PHP CODE: <?=$_FILES['otd']['error']?></div>
<? } ?>
<div style="font-size:24px; text-align:center">Direct Standard Process Service Order Transfer</div>
<form enctype="multipart/form-data" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
<input type="hidden" name="ip" value="<?=$_SERVER['REMOTE_ADDR'];?>">
<table align="center">
	<tr>
    	<td>Job Type</td>
    	<td><select name="affidavit_status"><?
$q3="SELECT DISTINCT affidavit_status from standard_packets WHERE affidavit_status <> ''";
$r3=@mysql_query($q3) or die("Query: $q3<br>".mysql_error());
while ($d3=mysql_fetch_array($r3, MYSQL_ASSOC)){
?>
<option><?=$d3[affidavit_status]?></option>
<? } ?>
</select></td>
	</tr>
	<tr>
    	<td>[or] New Job Type</td>
    	<td><input name="new_affidavit_status"></td>
	</tr>
	<tr>
    	<td>Papers to Serve</td>
    	<td><select name="addlDocs"><?
$q3="SELECT DISTINCT addlDocs from standard_packets WHERE addlDocs <> ''";
$r3=@mysql_query($q3) or die("Query: $q3<br>".mysql_error());
while ($d3=mysql_fetch_array($r3, MYSQL_ASSOC)){
?>
<option><?=$d3[addlDocs]?></option>
<? } ?>
</select></td>
	</tr>
	<tr>
    	<td>[or] New Papers to Serve</td>
    	<td><input name="new_addlDocs"></td>
	</tr>
	<tr>
    	<td>Attorney</td>
    	<td><select name="attid"><?
$rSub1 = @mysql_query("select attorneys_id, display_name from attorneys");
while ($dSub1 = mysql_fetch_array($rSub1, MYSQL_ASSOC)){
?>
<option value="<?=$dSub1[attorneys_id]?>">ID.<?=$dSub1[attorneys_id]?>/0 <?=$dSub1[display_name]?></option>
<? } ?>
</select></td>
	</tr>
	<tr>
    	<td>Case Number</td>
    	<td><input name="case_no"></td>
	</tr>
	<tr>
    	<td>File Number*</td>
    	<td><input name="client_file"></td>
	</tr>
	<tr>
    	<td>Papers to serve <em>.PDF</em></td>
    	<td><input size="60" name="otd" type="file" /></td>
	</tr>
    <tr>
    	<td>Special Instructions</td>
        <td><textarea cols="45" rows="4" name="attorney_notes"></textarea></td>
    </tr>
	<tr>
    	<td colspan="2" align="right"><input type="submit" name="submit" value="Start Order" style="background-color:#00ff00" /></td>
	</tr>
</table>

</form>
