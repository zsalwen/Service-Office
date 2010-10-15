<?php
mysql_connect();
mysql_select_db('logMonitor');
function sandbox($server){
	if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
	// log in at server1.example.com on port 22
	if(!($con = ssh2_connect($server, 22))){
		echo "fail: unable to establish connection\n";
	} else {
		// try to authenticate with username , password secretpassword
		if(!ssh2_auth_password($con, "", "")) {
			echo "fail: unable to authenticate\n";
		} else {
			// allright, we're in!
			//echo "okay: logged in...\n";
			
			// execute a command
			if(!($stream = ssh2_exec($con, "/sandbox/update" )) ){
				echo "fail: unable to execute command\n";
			} else{
				// collect returning data from command
				stream_set_blocking( $stream, true );
				$data = "";
				while( $buf = fread($stream,4096) ){
					$data .= $buf;
				}
				fclose($stream);
			}
		}
	}
	return $data;
}

function logTime($filename){
	if (file_exists($filename)) {
		return date ("F d Y H:i:s", filemtime($filename));
	}
}
function logDB($filename,$modTime){
		// here we will use the features to check for any modification and alert / run commands based there after, will run every 60 seconds?
		$r=@mysql_query("select * from changeWatch where file = '$filename'");
		$d=mysql_fetch_array($r,MYSQL_ASSOC);
		if ($modTime != $d[lastModTime]){
			@mysql_query("update changeWatch set lastModTime = '$modTime' where file = '$filename'");
			if ($d[runCommand]){
				
				$result = sandbox('mdws1.mdwestserve.com');
				
				mail('service@mdwestserve.com',$filename.'+'.time(),$result);
				//return 'Running '.$d[runCommand];
			}else{
				//return 'Nothing to do.';
			}
		}else{
			//return 'No Change.';
		}
}
logDB('/logs/user.log',logTime('/logs/user.log'));
logDB('/logs/contractor.log',logTime('/logs/contractor.log'));
logDB('/logs/webservice.log',logTime('/logs/webservice.log'));
logDB('/logs/client.log',logTime('/logs/client.log'));
logDB('/logs/courier.log',logTime('/logs/courier.log'));
logDB('/logs/debug.log',logTime('/logs/debug.log'));
logDB('/logs/download.log',logTime('/logs/download.log'));
logDB('/logs/mobile.log',logTime('/logs/mobile.log'));
logDB('/logs/source.log',logTime('/logs/source.log'));
logDB('/logs/cancelled.log',logTime('/logs/cancelled.log'));
?>