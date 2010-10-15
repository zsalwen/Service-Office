<?
function my_ssh($server,$command){
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
			if(!($stream = ssh2_exec($con,$command)) ){
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
		
	@mysql_query("INSERT INTO sshLog (commandString, commandServer) values ('$command','$server')");	
	}
	return $data;
}
if ($_GET[query]){
	$results = my_ssh('hwa1.hwestauctions.com',$_GET[query]);
}
?>

<div style="border:solid 3px #FF0000; padding:20px;" id="commands">

		<li><a href="?query=service httpd restart">Restart Web Server (apache)</a></li>
		<li><a href="?query=service mysqld restart">Restart Database (mysql)</a></li>
		<li><a href="?query=chmod -R 0777 /var/www">Repair 1 for PDF Generation</a></li>
		<li><a href="?query=rm -f /data/auction/queue/*">Remove Burson Unknown File Types</a></li>

	<ol>
		<li><a href="?query=sync">Prep memory activity</a></li>
		<li><a href="?query=echo 3 > /proc/sys/vm/drop_caches">Purge memory cache</a></li>
	</ol>
</div>
<div style="border:solid 3px #FF0000; padding:20px;" id="results"><pre><?=$results;?></pre></div>




