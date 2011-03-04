<? 
include 'common.php';

$user = $_COOKIE[psdata][user_id];
$packet = $_GET[packet];
$ip = $_SERVER['REMOTE_ADDR'];
$name=$_COOKIE['psdata']['name'];


mysql_select_db('core');








if ($_FILES['affidavit']){
	if ($_FILES['affidavit']['size'] < 10145728){
		if ($_FILES['affidavit']['size'] == 0){
			$ps = id2name($_COOKIE[psdata][user_id]);
			$error = "<div>Your file size registered as zero (due to oversized files).</div>";
		}else{
			// ok first we need to go get the files
			$path = "/data/service/scans/'.date('Y').'/'.date('F').'/'.date('j').'/";
			
 


if (!file_exists('/data/service/scans/'.date('Y'))){
mkdir ('/data/service/scans/'.date('Y'),0777);
}
if (!file_exists('/data/service/scans/'.date('Y').'/'.date('F'))){
mkdir ('/data/service/scans/'.date('Y').'/'.date('F'),0777);
}
if (!file_exists('/data/service/scans/'.date('Y').'/'.date('F').'/'.date('j'))){
mkdir ('/data/service/scans/'.date('Y').'/'.date('F').'/'.date('j'),0777);
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



<? if (isset($error)){ echo $error;} ?>
	
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

