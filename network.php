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
$host4 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.2 hostname');
$host5 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.3 hostname');
$host6 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.4 hostname');
$host7 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.6 hostname');
$results1 = my_ssh('mdws1.mdwestserve.com','w');
$results2 = my_ssh('mdws2.mdwestserve.com','w');
$results3 = my_ssh('ww2.mdwestserve.com','w');
$results4 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.2 w');
$results5 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.3 w');
$results6 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.4 w');
$results7 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.6 w');
?>
<table>
<tr>
<td>Host</td>
<td>w</td>

<tr>
<td><font size="+2"><?=$host1;?></font></td>
<td valign="top"><pre><?=$results1;?></pre></td>
</tr>

<tr>
<td><font size="+2"><?=$host2;?></font></td>
<td valign="top"><pre><?=$results2;?></pre></td>
</tr>

<tr>
<td><font size="+2"><?=$host3;?></font></td>
<td valign="top"><pre><?=$results3;?></pre></td>
</tr>

<tr>
<td><font size="+2"><?=$host4;?></font></td>
<td valign="top"><pre><?=$results4;?></pre></td>
</tr>

<tr>
<td><font size="+2"><?=$host5;?></font></td>
<td valign="top"><pre><?=$results5;?></pre></td>
</tr>

<tr>
<td><font size="+2"><?=$host6;?></font></td>
<td valign="top"><pre><?=$results6;?></pre></td>
</tr>

<tr>
<td><font size="+2"><?=$host7;?></font></td>
<td valign="top"><pre><?=$results7;?></pre></td>
</tr>

</table>
<?
}else{
header('Location: http://mdwestserve.com');
}
?>