<?
if ($_COOKIE[psdata][level] == 'Operations'){
mysql_connect();
mysql_select_db('core');
function valueData($key){
  $r=@mysql_query("select valueData from config where keyData = '$key'");
  $d=mysql_fetch_array($r,MYSQL_ASSOC);
  return $d[valueData];
}
function talk($to,$message){
	include_once '/thirdParty/xmpphp/XMPPHP/XMPP.php';
        $user = 'talkabout.files@gmail.com';
        $password = valueData($user);
	$conn = new XMPPHP_XMPP('talk.google.com', 5222, $user, $password, 'xmpphp', 'gmail.com', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
	try {
		$conn->useEncryption(true);
		$conn->connect();
		$conn->processUntil('session_start');
		//$conn->presence("Ya, I'm online","available","talk.google.com");
		$conn->message($to, $message);
		$conn->disconnect();
	} catch(XMPPHP_Exception $e) {
		die($e->getMessage());
	}
}

function getLast($commandString,$commandServer){
	$r=@mysql_query("select commandID, commandRun, commandOperator from sshLog where commandString = '$commandString' and commandServer = '$commandServer' order by commandID desc ");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
return $d[commandRun].'</td><td>'.$d[commandOperator];
}


function my_ssh($server,$command){
	if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
	// log in at server1.example.com on port 22
	if(!($con = ssh2_connect($server, 22))){
		echo "fail: unable to establish connection\n";
	} else {
		// try to authenticate with username , password secretpassword

$user=valueData('sshUser');	
$pass=valueData('sshPassword');
                if(!ssh2_auth_password($con, $user, $pass)) {
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
				talk('insidenothing@gmail.com',$_COOKIE[psdata][name].' SSH terminal: '.$server.' -> '.$command);
			}
		}
		
	@mysql_query("INSERT INTO sshLog (commandString, commandServer, commandRun, commandOperator) values ('$command','$server',NOW(),'".$_COOKIE['psdata']['name']."')");	
	}
	return $data;
}
if ($_POST[query] && $_POST[server]){
	$results = my_ssh($_POST[server],$_POST[query]);
}elseif ($_GET[query] && $_GET[server]){
	$results = my_ssh($_GET[server],$_GET[query]);
}
?>
<style>
	a { text-decoration:none;}
	td { font-size:12px;}
	body { font-size:10px;}
</style>	
<title>[SSH][<?=$_GET[query];?><?=$_POST[query];?>]</title>	
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="center" valign="top">
		<table cellpadding="0" cellspacing="0"><tr><td><b>mdws1.mdwestserve.com</b></td></tr>
		<?
		$r=@mysql_query("select distinct commandString from sshLog where commandServer = 'mdws1.mdwestserve.com' order by commandID DESC LIMIT 0,20");
		while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
			echo "<tr><td><a href='?server=mdws1.mdwestserve.com&query=$d[commandString]'>$d[commandString]</a></td><td>".getLast($d[commandString],'mdws1.mdwestserve.com')."</td></tr>";
		}
		?>
		</table>
		</td>
		<td align="center" valign="top">
		<table cellpadding="0" cellspacing="0"><tr><td><b>hwa1.hwestauctions.com</b></td></tr>
		<?
		$r=@mysql_query("select distinct commandString from sshLog where commandServer = 'hwa1.hwestauctions.com' order by commandID DESC LIMIT 0,20");
		while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
			echo "<tr><td><a href='?server=hwa1.hwestauctions.com&query=$d[commandString]'>$d[commandString]</a></td><td>".getLast($d[commandString],'hwa1.hwestauctions.com')."</td></tr>";
		}
		?>
		</table>
		</td>
		<td align="center" valign="top">
		<table cellpadding="0" cellspacing="0"><tr><td><b>alpha.mdwestserve.com</b></td></tr>
		<?
		$r=@mysql_query("select distinct commandString from sshLog where commandServer = 'alpha.mdwestserve.com' order by commandID DESC LIMIT 0,20");
		while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
			echo "<tr><td><a href='?server=alpha.mdwestserve.com&query=$d[commandString]'>$d[commandString]</a></td><td>".getLast($d[commandString],'alpha.mdwestserve.com')."</td></tr>";
		}
		?>
		</table>
		</td>
	</tr>
</table>



			<div style="border:solid 3px #cccccc;" id="query" align="center">
				<form method="post" action="ssh.php">
					<select name="server">
						<option><?=$_POST[server]?></option>
						<option>hwa1.hwestauctions.com</option>
						<option>mdws1.mdwestserve.com</option>
						<option>alpha.mdwestserve.com</option>
					</select>
					<input size="50" name="query" value="<?=$_POST[query];?>">
					<input type="submit" value="Run New Command">
				</form>
			</div>

<div style="border:solid 3px #FFFF00; padding:20px; background-color:#FFFFcc;" id="results"><pre><?=$results;?></pre></div>






<?
}else{
header('Location: http://mdwestserve.com');
}
?>