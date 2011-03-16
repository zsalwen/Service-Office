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
			}
		}
	}
	return $data;
}

$host1 = my_ssh('mdws1.mdwestserve.com','hostname');
$host2 = my_ssh('mdws2.mdwestserve.com','hostname');
$host3 = my_ssh('ww2.mdwestserve.com','hostname');
$results1 = my_ssh('mdws1.mdwestserve.com','ps -e');
$results2 = my_ssh('mdws2.mdwestserve.com','ps -e');
$results3 = my_ssh('ww2.mdwestserve.com','ps -e');
?>
<table>
<tr>
<td><font size="+2"><?=$host1;?></font></td>
<td><font size="+2"><?=$host2;?></font></td>
<td><font size="+2"><?=$host3;?></font></td>
</tr>
<tr>
<td valign="top"><pre><?=$results1;?></pre></td>
<td valign="top"><pre><?=$results2;?></pre></td>
<td valign="top"><pre><?=$results3;?></pre></td>
</tr>
</table>
<?
}else{
header('Location: http://mdwestserve.com');
}
?>