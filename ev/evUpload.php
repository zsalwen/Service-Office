<? 
include 'common.php';

$user = $_COOKIE[psdata][user_id];
$eviction = $_GET[eviction];
$ip = $_SERVER['REMOTE_ADDR'];
$name=$_COOKIE['psdata']['name'];
if ($_POST[tab]){
$tab = $_POST[tab];
}elseif($_GET[tab]){
$tab = $_GET[tab];
}else{
$tab = "1";
}
mysql_select_db('service');
$q="SELECT eviction_id, name1, name2, name3, name4, name5, name6, address1, city1, state1, zip1, address2, city2, state2, zip2, address3, city3, state3, zip3, address4, city4, state4, zip4, address5, city5, state5, zip5, address6, city6, state6, zip6, case_no, circuit_court, client_file, date_received, attorneys_id FROM evictionPackets WHERE eviction_id = '$eviction'";
$r=@mysql_query($q);
$d=mysql_fetch_array($r, MYSQL_ASSOC);

function washURI2($uri){
$return = str_replace('/ps','',$uri);
return $return;
}


if ($_GET['delete']){
	mysql_select_db('service');
	$qd="DELETE from ps_affidavits where affidavitID = '$_GET[delete]'";
	$rd=@mysql_query($qd) or die("Query: $qd<br>".mysql_error());
	timeline($eviction,$_COOKIE[psdata][name]." Removed Scan #$_GET[delete]");
	header('Location: evUpload.php?eviction='.$eviction.'&tab='.$tab);
}

if (isset($_GET['received'])){
	//@mysql_query("update ps_evictions set affidavit_status='RECEIVED' where eviction_id = '$_GET[received]' ");
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
			$target_path = $file_path."/".$eviction.".".$tab.".".time().".pdf";  
			if(move_uploaded_file($_FILES['affidavit']['tmp_name'], $target_path)) {
			}

			$link1 = "http://mdwestserve.com/ps/affidavits/".$eviction.".".$tab.".".time().".pdf"; 
			if ($_POST[method] != 'Freeform'){
				$method=$_POST[method];
			}else{
				$method=$_POST[freeform];
			}
			$eviction = "EV".$_GET[eviction];
			$query = "INSERT into ps_affidavits (packetID, defendantID, affidavit, userID, method, uploadDate) VALUES ('$eviction','$tab','$link1','$user','$method', NOW())";
			mysql_select_db('service');

			@mysql_query($query);
				timeline($eviction,$_COOKIE[psdata][name]." Scanned $method");
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
	header('Location: evUpload.php?eviction='.$eviction.'&tab='.$tab);
}

//include 'menu.php';
$i=0;
?>
<table border="1" style="border-collapse:collapse" width="100%">
<tr>
<?
while ($i < 5) {$i++;
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
        <a class="ser" href="evUpload.php?eviction=<?=$eviction?>&tab=<?=$i?>"><?=ucwords(strtolower($d["name$i"]))?></a>
        </td>
<?
	}
}
?>
</tr>
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
	if ($d[address2c] != ''){
		$add2c=$d[address2c].'<br>'.$d[city2c].', '.$d[state2c].' '.$d[zip2c];
		$add2cx=$d[address2c].' '.$d[city2c].', '.$d[state2c].' '.$d[zip2c];
	}
	if ($d[address2d] != ''){
		$add2d=$d[address2d].'<br>'.$d[city2d].', '.$d[state2d].' '.$d[zip2d];
		$add2dx=$d[address2d].' '.$d[city2d].', '.$d[state2d].' '.$d[zip2d];
	}
	if ($d[address2e] != ''){
		$add2e=$d[address2e].'<br>'.$d[city2e].', '.$d[state2e].' '.$d[zip2e];
		$add2ex=$d[address2e].' '.$d[city2e].', '.$d[state2e].' '.$d[zip2e];
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
	if ($d[address3c] != ''){
		$add3c=$d[address3c].'<br>'.$d[city3c].', '.$d[state3c].' '.$d[zip3c];
		$add3cx=$d[address3c].' '.$d[city3c].', '.$d[state3c].' '.$d[zip3c];
	}
	if ($d[address3d] != ''){
		$add3d=$d[address3d].'<br>'.$d[city3d].', '.$d[state3d].' '.$d[zip3d];
		$add3dx=$d[address3d].' '.$d[city3d].', '.$d[state3d].' '.$d[zip3d];
	}
	if ($d[address3e] != ''){
		$add3e=$d[address3e].'<br>'.$d[city3e].', '.$d[state3e].' '.$d[zip3e];
		$add3ex=$d[address3e].' '.$d[city3e].', '.$d[state3e].' '.$d[zip3e];
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
	if ($d[address4c] != ''){
		$add4c=$d[address4c].'<br>'.$d[city4c].', '.$d[state4c].' '.$d[zip4c];
		$add4cx=$d[address4c].' '.$d[city4c].', '.$d[state4c].' '.$d[zip4c];
	}
	if ($d[address4d] != ''){
		$add4d=$d[address4d].'<br>'.$d[city4d].', '.$d[state4d].' '.$d[zip4d];
		$add4dx=$d[address4d].' '.$d[city4d].', '.$d[state4d].' '.$d[zip4d];
	}
	if ($d[address4e] != ''){
		$add4e=$d[address4e].'<br>'.$d[city4e].', '.$d[state4e].' '.$d[zip4e];
		$add4ex=$d[address4e].' '.$d[city4e].', '.$d[state4e].' '.$d[zip4e];
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
	if ($d[address5c] != ''){
		$add5c=$d[address5c].'<br>'.$d[city5c].', '.$d[state5c].' '.$d[zip5c];
		$add5cx=$d[address5c].' '.$d[city5c].', '.$d[state5c].' '.$d[zip5c];
	}
	if ($d[address5d] != ''){
		$add5d=$d[address5d].'<br>'.$d[city5d].', '.$d[state5d].' '.$d[zip5d];
		$add5dx=$d[address5d].' '.$d[city5d].', '.$d[state5d].' '.$d[zip5d];
	}
	if ($d[address5e] != ''){
		$add5e=$d[address5e].'<br>'.$d[city5e].', '.$d[state5e].' '.$d[zip5e];
		$add5ex=$d[address5e].' '.$d[city5e].', '.$d[state5e].' '.$d[zip5e];
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
	if ($d[address6c] != ''){
		$add6c=$d[address6c].'<br>'.$d[city6c].', '.$d[state6c].' '.$d[zip6c];
		$add6cx=$d[address6c].' '.$d[city6c].', '.$d[state6c].' '.$d[zip6c];
	}
	if ($d[address6d] != ''){
		$add6d=$d[address6d].'<br>'.$d[city6d].', '.$d[state6d].' '.$d[zip6d];
		$add6dx=$d[address6d].' '.$d[city6d].', '.$d[state6d].' '.$d[zip6d];
	}
	if ($d[address6e] != ''){
		$add6e=$d[address6e].'<br>'.$d[city6e].', '.$d[state6e].' '.$d[zip6e];
		$add6ex=$d[address6e].' '.$d[city6e].', '.$d[state6e].' '.$d[zip6e];
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
	if ($d[address1c] != ''){
		$add1c=$d[address1c].'<br>'.$d[city1c].', '.$d[state1c].' '.$d[zip1c];
		$add1cx=$d[address1c].' '.$d[city1c].', '.$d[state1c].' '.$d[zip1c];
	}
	if ($d[address1d] != ''){
		$add1d=$d[address1d].'<br>'.$d[city1d].', '.$d[state1d].' '.$d[zip1d];
		$add1dx=$d[address1d].' '.$d[city1d].', '.$d[state1d].' '.$d[zip1d];
	}
	if ($d[address1e] != ''){
		$add1e=$d[address1e].'<br>'.$d[city1e].', '.$d[state1e].' '.$d[zip1e];
		$add1ex=$d[address1e].' '.$d[city1e].', '.$d[state1e].' '.$d[zip1e];
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
	if ($add1cx){ echo '<tr><td align="LEFT">'.$add1cx.'</tr></td>'; }
	if ($add1dx){ echo '<tr><td align="LEFT">'.$add1dx.'</tr></td>'; }
	if ($add1ex){ echo '<tr><td align="LEFT">'.$add1ex.'</tr></td>'; }
	$court=ucwords(strtolower($d['circuit_court']));
	echo '<tr><td align="left">Circuit Court For '.$court.' County</tr></td>';
	echo '<tr><td align="left">'.id2attorney($d['attorneys_id']).'</tr></td>';
?>
</table>
</div></td>
<? /*
<td rowspan="2" ><div id="affidavits" style="height:473px; width:405px; overflow:auto; background-color:#FFCC99"><center>
<?

mysql_select_db('service');


$q5="SELECT affidavit, method, affidavitID FROM ps_affidavits WHERE packetID = '$eviction' and defendantID = '$tab'";
$r5=@mysql_query($q5) or die ("Query: $q5<br>".mysql_error());
while ($d5=mysql_fetch_array($r5, MYSQL_ASSOC)){
		echo $d5[method].' - <a target="_blank" href="'.$d5[affidavit].'">'.$name.'</a><div style="float:right; background-color:99CCFF; padding:2px"><a class="spo" href="evUpload.php?eviction='.$d[eviction_id].'&delete='.$d5[affidavitID].'&tab='.$tab.'"><small>DELETE</small></a></div><hr>';


	 }
?>
</center></div></td> */ ?></tr>

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
    <input type="hidden" name="eviction" value="<?=$eviction?>" />
    	<? if($_POST[select] == 'Freeform'){ ?>
    	<tr>
        	<td align="center">Freeform: <input size="50" name="freeform" /></td>
        </tr>
        <? } ?>
        <tr>
            <td align="center"><? if($_POST[select]){?><input size="50" name="affidavit" type="file" /><? }?></td>
        </tr>
    
        <tr align="center">
            <td colspan="2"><? if($_POST[select]){?><input type="submit" name="submit" value="Upload PDF of '<?=$_POST[select]?>' for Logic No. <?=$eviction?>-<?=$tab?>" /><? } ?></td>
        </tr>
    </form>
    </table> 
</div></td></tr>
	
</table>
<?
mysql_select_db('service');

if (strpos($eviction,"EV")){}else{
	$eviction="EV".$eviction;
}
$q5="SELECT * FROM ps_affidavits WHERE packetID = '$eviction' and defendantID = '$tab'";
$r5=@mysql_query($q5) or die ("Query: $q5<br>".mysql_error());
while ($d5=mysql_fetch_array($r5, MYSQL_ASSOC)){
		echo "<li>".$d5[method].' - <a target="_blank" href="'.washURI2($d5[affidavit]).'">'.$d5[uploadDate].'</a> - <a href="evUpload.php?eviction='.$d[eviction_id].'&delete='.$d5[affidavitID].'&tab='.$tab.'"><small>REMOVE</small></a> - <a href="evRename.php?id='.$d5[affidavitID].'&eviction='.$d[eviction_id].'"><small>RENAME</small></a></li>';
}
mysql_close();
?>
