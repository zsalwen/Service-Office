<? 
include 'common.php';

$user = $_COOKIE[psdata][user_id];
$packet = $_GET[packet];
$ip = $_SERVER['REMOTE_ADDR'];
$name=$_COOKIE['psdata']['name'];
if ($_POST[tab]){
$tab = $_POST[tab];
}elseif($_GET[tab]){
$tab = $_GET[tab];
}else{
$tab = "1";
}
mysql_select_db('core');
$q="SELECT packet_id, name1, name2, name3, name4, name5, name6, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, case_no, circuit_court, client_file, date_received, attorneys_id FROM ps_packets WHERE packet_id = '$packet'";
$r=@mysql_query($q);
$d=mysql_fetch_array($r, MYSQL_ASSOC);

function washURI2($uri){
$return = str_replace('/ps','',$uri);
return $return;
}


if ($_GET['delete']){
	mysql_select_db('core');
	$qd="DELETE from ps_affidavits where affidavitID = '$_GET[delete]'";
	$rd=@mysql_query($qd) or die("Query: $qd<br>".mysql_error());
	timeline($packet,$_COOKIE[psdata][name]." Removed Scan #$_GET[delete]");
	header('Location: affidavitUpload.php?packet='.$packet.'&tab='.$tab);
}

if (isset($_GET['received'])){
	//@mysql_query("update ps_packets set affidavit_status='RECEIVED' where packet_id = '$_GET[received]' ");
	//header('Location: affidavitManager.php?server=operations');
}


if ($_FILES['affidavit']){
	if ($_FILES['affidavit']['size'] < 10145728){
		if ($_FILES['affidavit']['size'] == 0){
			$ps = id2name($_COOKIE[psdata][user_id]);
			$error = "<div>Your file size registered as zero (due to oversized files).</div>";
		}else{
			// ok first we need to go get the files
			$path = "/data/service/scans/";
			if (!file_exists($path)){
				mkdir ($path,0777);
			}
 
			$file_path = $path;
			if (!file_exists($file_path)){
				mkdir ($file_path,0777);
			}
			$ext = explode('.', $_FILES['affidavit']['name']);
			$target_path = $file_path."/".$packet.".".$tab.".".time().".pdf";  
			if(move_uploaded_file($_FILES['affidavit']['tmp_name'], $target_path)) {
			}

			$link1 = "http://mdwestserve.com/ps/affidavits/".$packet.".".$tab.".".time().".pdf"; 
			if ($_POST[method] != 'Freeform'){
				$method=$_POST[method];
			}else{
				$method=$_POST[freeform];
			}
			$query = "INSERT into ps_affidavits (packetID, defendantID, affidavit, userID, method, uploadDate) VALUES ('$packet','$tab','$link1','$user','$method', NOW())";
			mysql_select_db('core');

			@mysql_query($query);
				timeline($packet,$_COOKIE[psdata][name]." Scanned $method");
				psActivity('docUpload');
		}
		}else{
			$ps = id2name($_COOKIE[psdata][user_id])."<br>".$link1;
			$error = "<div>Your file size was too large.</div>";
			$message = "<table>";
			foreach($_FILES as $key => $value){
			$message .="<tr><td>$key</td><td>$value</td></tr>";
			}
			foreach($_SERVER as $key => $value){
			$message .="<tr><td>$key</td><td>$value</td></tr>";
			}
			$message .="</table>";
			$error .= $message;
	}
	header('Location: affidavitUpload.php?packet='.$packet.'&tab='.$tab);
}

//include 'menu.php';
mysql_select_db('core');
$i=0;
?>
<table border="1" style="border-collapse:collapse" width="100%">
<tr>
<?
while ($i < 6) {$i++;

if ($d["name$i"]){
	if (!$_GET[tab] && $i == 1){
		$color = '#000099';
	}elseif ($_GET[tab] == "$i"){
		$color = '#000099';
	}else{
		$color = '#0066FF';
	}
?>
	<td align="center" bgcolor="<?=$color?>" style="font-size:18px; border:inset 4px #0099FF;">
	<a class="ser" href="affidavitUpload.php?packet=<?=$packet?>&tab=<?=$i?>"><?=ucwords(strtolower($d["name$i"]))?></a>
	</td>

<?
	}
}
?>
</tr>
<?
$i=0;
?>
</table>
<?

if ($tab == "2"){
	$name = $d[name2];
	$add1 = $d[address2].'<br>'.$d[city2].', '.$d[state2].' '.$d[zip2];
	$add1x = $d[address2].' '.$d[city2].', '.$d[state2].' '.$d[zip2];
	if ($d[address2a] != ''){
		$add1a=$d[address2a].'<br>'.$d[city2a].', '.$d[state2a].' '.$d[zip2a];
		$add1ax=$d[address2a].' '.$d[city2a].', '.$d[state2a].' '.$d[zip2a];
	}
	if ($d[address2b] != ''){
		$add1b=$d[address2b].'<br>'.$d[city2b].', '.$d[state2b].' '.$d[zip2b];
		$add1bx=$d[address2b].' '.$d[city2b].', '.$d[state2b].' '.$d[zip2b];
	}
} elseif ($tab == "3"){
	$name = $d[name3];
	$add1 = $d[address3].'<br>'.$d[city3].', '.$d[state3].' '.$d[zip3];
	$add1x = $d[address3].' '.$d[city3].', '.$d[state3].' '.$d[zip3];
	if ($d[address3a] != ''){
		$add1a=$d[address3a].'<br>'.$d[city3a].', '.$d[state3a].' '.$d[zip3a];
		$add1ax=$d[address3a].' '.$d[city3a].', '.$d[state3a].' '.$d[zip3a];
	}
	if ($d[address3b] != ''){
		$add1b=$d[address3b].'<br>'.$d[city3b].', '.$d[state3b].' '.$d[zip3b];
		$add1bx=$d[address3b].' '.$d[city3b].', '.$d[state3b].' '.$d[zip3b];
	}
} elseif ($tab == "4"){
	$name = $d[name4];
	$add1 = $d[address4].'<br>'.$d[city4].', '.$d[state4].' '.$d[zip4];
	$add1x = $d[address4].' '.$d[city4].', '.$d[state4].' '.$d[zip4];
	if ($d[address4a] != ''){
		$add1a=$d[address4a].'<br>'.$d[city4a].', '.$d[state4a].' '.$d[zip4a];
		$add1ax=$d[address4a].' '.$d[city4a].', '.$d[state4a].' '.$d[zip4a];
	}
	if ($d[address4b] != ''){
		$add1b=$d[address4b].'<br>'.$d[city4b].', '.$d[state4b].' '.$d[zip4b];
		$add1bx=$d[address4b].' '.$d[city4b].', '.$d[state4b].' '.$d[zip4b];
	}
} elseif ($tab == "5"){
	$name = $d[name5];
	$add1 = $d[address5].'<br>'.$d[city5].', '.$d[state5].' '.$d[zip5];
	$add1x = $d[address5].' '.$d[city5].', '.$d[state5].' '.$d[zip5];
	if ($d[address5a] != ''){
		$add1a=$d[address5a].'<br>'.$d[city5a].', '.$d[state5a].' '.$d[zip5a];
		$add1ax=$d[address5a].' '.$d[city5a].', '.$d[state5a].' '.$d[zip5a];
	}
	if ($d[address5b] != ''){
		$add1b=$d[address5b].'<br>'.$d[city5b].', '.$d[state5b].' '.$d[zip5b];
		$add1bx=$d[address5b].' '.$d[city5b].', '.$d[state5b].' '.$d[zip5b];
	}
} elseif ($tab == "6"){
	$name = $d[name6];
	$add1 = $d[address6].'<br>'.$d[city6].', '.$d[state6].' '.$d[zip6];
	$add1x = $d[address6].' '.$d[city6].', '.$d[state6].' '.$d[zip6];
	if ($d[address6a] != ''){
		$add1a=$d[address6a].'<br>'.$d[city6a].', '.$d[state6a].' '.$d[zip6a];
		$add1ax=$d[address6a].' '.$d[city6a].', '.$d[state6a].' '.$d[zip6a];
	}
	if ($d[address6b] != ''){
		$add1b=$d[address6b].'<br>'.$d[city6b].', '.$d[state6b].' '.$d[zip6b];
		$add1bx=$d[address6b].' '.$d[city6b].', '.$d[state6b].' '.$d[zip6b];
	}
} else {
	$name = $d[name1];
	$add1 = $d[address1].'<br>'.$d[city1].', '.$d[state1].' '.$d[zip1];
	$add1x = $d[address1].' '.$d[city1].', '.$d[state1].' '.$d[zip1];
	if ($d[address1a] != ''){
		$add1a=$d[address1a].'<br>'.$d[city1a].', '.$d[state1a].' '.$d[zip1a];
		$add1ax=$d[address1a].' '.$d[city1a].', '.$d[state1a].' '.$d[zip1a];
	}
	if ($d[address1b] != ''){
		$add1b=$d[address1b].'<br>'.$d[city1b].', '.$d[state1b].' '.$d[zip1b];
		$add1bx=$d[address1b].' '.$d[city1b].', '.$d[state1b].' '.$d[zip1b];
	}
}
?>
<style>
body { padding:0px;
margin:0px;}
a.ser{color:#FFFFFF; text-decoration:none;}
a.ser:hover{color:#FF0000; text-decoration:none;}
a.spo{text-decoration:none; color:#CC0000}
a.spo:hover{text-decoration:none; color:#FF0000}
a.spu{text-decoration:none; font-size:15px;}
</style>
<table border="1" style="border-collapse:collapse;" cellpadding="0" align="center"><tr><td>

<div id="tabinfo" style="background-color:#CC99FF; overflow:auto">
<table align="left">
<? if (isset($error)){ echo $error;}
	echo '<tr><td align="left"><strong>'.ucwords(strtolower($name)).'</strong> '.$d['case_no'].'</td></tr>';
	echo '<tr><td align="left">'.$add1x.'</td></tr>';
	if ($add1ax){ echo '<tr><td align="left">'.$add1ax.'</tr></td>'; }
	if ($add1bx){ echo '<tr><td align="LEFT">'.$add1bx.'</tr></td>'; }
	$court=ucwords(strtolower($d['circuit_court']));
	echo '<tr><td align="left">Circuit Court For '.$court.' County</tr></td>';
	echo '<tr><td align="left">'.id2attorney($d['attorneys_id']).'</tr></td>';
?>
</table>
</div></td></tr>
<tr><td>
<div id="update" style="padding:5px; background-color:#99CCFF">
    <table align="center" width="100%">
    <form method="post" name="select">
    <tr>
    	<td align="center" height="50px" valign="top"><? if(!$_POST[select]){?><select onchange="this.form.submit()" name="select"><option>Select From Below</option>
                <option>Certified Mail Receipt</option>
                <option>Copy of out of state affidavit.</option>
				<option>Complete return from court</option>
                <option>Faxed Return from court</option>
				<option>Occupant Notice Return from court</option>
                <option>Out of State Affidavit of Attempted Service</option>
                <option>Out of State Affidavit of Personal Delivery</option>
                <option>Picture of Property</option>
                <option>Return from court</option>
				<option>Return from court-Attempts and Posting</option>
				<option>Return from court-Attempts Only</option>
				<option>Return from court-Mailing Only</option>
				<option>Return from court-Posting Only</option>
				<option>Returned Certified Mail</option>
                <option>Returned First Class Mail</option>
                <option>Signed Return Receipt</option>
				<option>Server Notes</option>
				<option>Unsigned Return Receipt</option>
        <option>Freeform</option></select><? } ?></td>
    </form>
    </tr>
    <form enctype="multipart/form-data" method="post" name="upload">
	<input type="hidden" name="method" value="<?=$_POST[select]?>" />
    <input type="hidden" name="defendant" value="<?=$name?>" />
    <input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
    <input type="hidden" name="case_no" value="<?=$d[case_no]?>" />
    <input type="hidden" name="tab" value="<?=$tab?>" />
    <input type="hidden" name="packet" value="<?=$packet?>" />
    	<? if($_POST[select] == 'Freeform'){ ?>
    	<tr>
        	<td align="center">Freeform: <input size="50" name="freeform" /></td>
        </tr>
        <? } ?>
        <tr>
            <td align="center"><? if($_POST[select]){?><input size="50" name="affidavit" type="file" /><? }?></td>
        </tr>
    
        <tr align="center">
            <td colspan="2"><? if($_POST[select]){?><input type="submit" name="submit" value="Upload PDF of '<?=$_POST[select]?>' for Logic No. <?=$packet?>-<?=$tab?>" /><? } ?></td>
        </tr>
    </form>
    </table> 
</div></td></tr>
	
</table>
<?
mysql_select_db('core');


$q5="SELECT * FROM ps_affidavits WHERE packetID = '$packet' and defendantID = '$tab'";
$r5=@mysql_query($q5) or die ("Query: $q5<br>".mysql_error());
while ($d5=mysql_fetch_array($r5, MYSQL_ASSOC)){
		echo "<li>".$d5[method].' - <a target="_blank" href="'.washURI2($d5[affidavit]).'">'.$d5[uploadDate].'</a> - <a href="affidavitUpload.php?packet='.$d[packet_id].'&delete='.$d5[affidavitID].'&tab='.$tab.'"><small>REMOVE</small></a> - <a href="affidavitRename.php?id='.$d5[affidavitID].'&packet='.$packet.'"><small>RENAME</small></a></li>';


	 }


//include 'footer.php';
?>
