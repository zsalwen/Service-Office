<?
if ($_COOKIE[psdata][level] == 'Operations'){
mysql_connect();
mysql_select_db('core');
function valueData($key){
  $r=@mysql_query("select valueData from config where keyData = '$key'");
  $d=mysql_fetch_array($r,MYSQL_ASSOC);
  return $d[valueData];
}
function my_ssh($server,$command){
	if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
	if(!($con = ssh2_connect($server, 22))){
		echo "fail: unable to establish connection\n";
	} else {
                $user=valueData('sshUser');	
                $pass=valueData('sshPassword');
                if(!ssh2_auth_password($con, $user, $pass)) {
			echo "fail: unable to authenticate\n";
		} else {
			if(!($stream = ssh2_exec($con,$command)) ){
				echo "fail: unable to execute command\n";
			} else{
				stream_set_blocking( $stream, true );
				$data = "";
				while( $buf = fread($stream,4096) ){
					$data .= $buf;
				}
				fclose($stream);
				talk('insidenothing@gmail.com',$_COOKIE[psdata][name].' SSH terminal: '.$server.' -> '.$command);
			}
		}
	}
	return $data;
}

$results1 = my_ssh('mdws1.mdwestserve.com','ps -e');
$results2 = my_ssh('mdws2.mdwestserve.com','ps -e');
$results3 = my_ssh('ww2.mdwestserve.com','ps -e');
//$results4 = my_ssh('mdws1.mdwestserve.com','ps -e');
?>
<table>
<tr>
<td>mdws1</td>
<td>mdws2</td>
<td>db1</td>
<td>db2</td>
</tr>
<tr>
<td><pre><?=$results1;?></pre></td>
<td><pre><?=$results2;?></pre></td>
<td><pre><?=$results3;?></pre></td>
<td><pre><?=$results4;?></pre></td>
</tr>
</table>
<?
}else{
header('Location: http://mdwestserve.com');
}
?>