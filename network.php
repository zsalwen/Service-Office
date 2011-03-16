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
$host3 = my_ssh('ww2.mdwestserve.com','hostname');
$host4 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.2 hostname');
$host5 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.3 hostname');
$host6 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.4 hostname');
$host7 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.6 hostname');
// test for httpd
$results1 = my_ssh('mdws1.mdwestserve.com',' service httpd status');
$results3 = my_ssh('ww2.mdwestserve.com','service httpd status');
$results4 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.2 service httpd status');
$results5 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.3 service httpd status');
$results6 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.4 service httpd status');
$results7 = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.6 service httpd status');
// test for mysqld
$results1a = my_ssh('mdws1.mdwestserve.com',' service mysqld status');
$results3a = my_ssh('ww2.mdwestserve.com','service mysqld status');
$results4a = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.2 service mysqld status');
$results5a = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.3 service mysqld status');
$results6a = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.4 service mysqld status');
$results7a = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.6 service mysqld status');

// get server names running on our web servers 
$results1b = my_ssh('mdws1.mdwestserve.com','grep ServerName /etc/httpd/conf/httpd.conf');
$results3b = my_ssh('ww2.mdwestserve.com','grep ServerName /etc/httpd/conf/httpd.conf');
$results5b = my_ssh('ww2.mdwestserve.com','ssh 10.0.0.3 grep ServerName /etc/httpd/conf/httpd.conf');
?>
<table border="1">
<tr>
<td>Host</td>
<td>httpd</td>
<td>mysqld</td>
</tr>

<tr>
<td><pre><?=$host1;?></pre></td>
<td valign="top"><pre><?=$results1;?></pre></td>
<td valign="top"><pre><?=$results1a;?></pre></td>
</tr>


<tr>
<td><pre><?=$host3;?></pre></td>
<td valign="top"><pre><?=$results3;?></pre></td>
<td valign="top"><pre><?=$results3a;?></pre></td>
</tr>

<tr>
<td><pre><?=$host4;?></pre></td>
<td valign="top"><pre><?=$results4;?></pre></td>
<td valign="top"><pre><?=$results4a;?></pre></td>
</tr>

<tr>
<td><pre><?=$host5;?></pre></td>
<td valign="top"><pre><?=$results5;?></pre></td>
<td valign="top"><pre><?=$results5a;?></pre></td>
</tr>

<tr>
<td><pre><?=$host6;?></pre></td>
<td valign="top"><pre><?=$results6;?></pre></td>
<td valign="top"><pre><?=$results6a;?></pre></td>
</tr>

<tr>
<td><pre><?=$host7;?></pre></td>
<td valign="top"><pre><?=$results7;?></pre></td>
<td valign="top"><pre><?=$results7a;?></pre></td>
</tr>

</table>

<table border="1">
<tr>
<td><pre><?=$host1;?></pre></td>
<td><pre><?=$host3;?></pre></td>
<td><pre><?=$host5;?></pre></td>
</tr>
<tr>
<td valign="top"><pre><?=$results1b;?></pre></td>
<td valign="top"><pre><?=$results3b;?></pre></td>
<td valign="top"><pre><?=$results5b;?></pre></td>
</tr>
</table>

<?
}else{
header('Location: http://mdwestserve.com');
}
?>