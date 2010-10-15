<center><h1>Re-Upload Scans</h1></center>
<table border="1" style="border-collapse:collapse;" align='center'>
<?


$i=0;

function id2name($id){
	$q="SELECT name FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[name];
}

function byteConvert(&$bytes){
        $b = (int)$bytes;
        $s = array('B', 'kB', 'MB', 'GB', 'TB');
        if($b < 0){
            return "0 ".$s[0];
        }
        $con = 1024;
        $e = (int)(log($b,$con));
        return '<b>'.number_format($b/pow($con,$e),0,',','.').' '.$s[$e].'</b>'; 
}

function testLink($file){
	$file = str_replace('http://mdwestserve.com/ps/affidavits/','/data/service/scans/',$file);
	$file = str_replace('http://mdwestserve.com/affidavits/','/data/service/scans/',$file);
	if(file_exists($file)){
		$size = filesize($file);
		return 0;
	}else{
		return 1;
	}
}

mysql_connect();
mysql_select_db('core');


$r=@mysql_query("select * from ps_affidavits order by uploadDate DESC");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
if (testLink($d[affidavit]) == 1){
$i++;
?>

<tr><td><?=$i?></td><td><?=$d[uploadDate]?></td><td><?=$d[packetID]?></td><td><?=$d[defendantID]?></td><td><?=$d[method]?></td><td><?=$d[affidavit];?></td></tr>

<? }}
echo "</table>";
if ($_COOKIE[psdata][level] == 'Operations'){
	echo '<hr>';
	include 'photoCheck.php';
}
?>