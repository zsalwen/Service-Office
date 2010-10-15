<?


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
			echo "okay: logged in...\n";
			
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
?>


mdws1.mdwestserve.com
<pre>
<?=sandbox('mdws1.mdwestserve.com');?>
</pre>
<hr>
hwa1.hwestauctions.com
<pre>
<?=sandbox('hwa1.hwestauctions.com');?>
</pre>