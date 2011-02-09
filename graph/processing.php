<?
if ($_COOKIE[psdata][level] == 'Operations'){

function search($mystring,$findme){
	$pos = strpos($mystring, $findme);
	if ($pos === false) {
		return 0;
	} else {
		return 1;
	}
}




?>
<form method="POST"><select name="log">
<?
if($_POST[log]){
	echo "<option>$_POST[log]</option>";
}



$directory = '/logs';
    $results = array();
    $handler = opendir($directory);
    while ($file = readdir($handler)) {
        if ($file != '.' && $file != '..' && $file != 'CVS')
            echo "<option>$file</option>";
    }

    closedir($handler);
?>
</select><input name="search" value="<?=$_POST[search]?>"><input type="submit" value="Scan Log File">
</form>

<?

echo "<fieldset><ol>";
$file = fopen("/logs/$_POST[log]", "r") or exit("Unable to open file!");
//Output a line of the file until the end is reached
while(!feof($file))
  {
  $line = fgets($file);

    if ($_POST[search]){

	if(search($line,$_POST[search]) == 1){
	echo "<li>".$line."</li>";
	
	}
  
  		}else{
	echo "<li>".$line."</li>";
	
	}
}
fclose($file);
echo "</ol><legend>Search for $_POST[search] in $_POST[log]</legend></fieldset>";






/*
mysql_connect();
mysql_select_db('service');
include "functions.php";
$i=0;
//z will be the largest number encountered
$z=0;
$zi=0;
while ($i < 12){$i++;$zi++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT packet_id FROM ps_packets WHERE date_received LIKE '%2009-$i2%'");
	$received08["$i"]=mysql_num_rows($r);
	if ($received08["$i"] > 0){}else{
		$received08["$i"]='0';
	}
	if ($received08["$i"] > $z){
		$z=$received08["$i"];
		$zz=$zi-1;
		$zzz="-".monthConvert($i2)." '09";
	}
	if ($src == ''){
		$src = $received08["$i"];
	}else{
		$src .= ','.$received08["$i"];
	}
	$src2 .= '|'.monthConvert($i2)." '09";
}
$i=0;
while ($i < 12){$i++;$zi++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT packet_id FROM ps_packets WHERE date_received LIKE '%2010-$i2%'");
	$received09["$i"]=mysql_num_rows($r);
	if ($received09["$i"] > 0){}else{
		$received09["$i"]='0';
	}
	if ($received09["$i"] > $z){
		$z=$received09["$i"];
		$zz=$zi-1;
		$zzz="-".monthConvert($i2)." '10";
	}
	if ($src == ''){
		$src = $received09["$i"];
	}else{
		$src .= ','.$received09["$i"];
	}
	$src2 .= '|'.monthConvert($i2)." '10";
}
//pull BURSON files
$i=0;
while ($i < 12){$i++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT packet_id FROM ps_packets WHERE date_received LIKE '%2009-$i2%' AND attorneys_id='1'");
	$value=mysql_num_rows($r);
	if ($value > 0){}else{
		$value='0';
	}
	if ($burson == ''){
		$burson = $value;
	}else{
		$burson .= ','.$value;
	}
}
$i=0;
while ($i < 12){$i++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT packet_id FROM ps_packets WHERE date_received LIKE '%2010-$i2%' AND attorneys_id='1'");
	$value=mysql_num_rows($r);
	if ($value > 0){}else{
		$value='0';
	}
	if ($burson == ''){
		$burson = $value;
	}else{
		$burson .= ','.$value;
	}
}
//pull WHITE files
$i=0;
while ($i < 12){$i++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT packet_id FROM ps_packets WHERE date_received LIKE '%2009-$i2%' AND attorneys_id='3'");
	$value=mysql_num_rows($r);
	if ($value > 0){}else{
		$value='0';
	}
	if ($white == ''){
		$white = $value;
	}else{
		$white .= ','.$value;
	}
}
$i=0;
while ($i < 12){$i++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT packet_id FROM ps_packets WHERE date_received LIKE '%2010-$i2%' AND attorneys_id='3'");
	$value=mysql_num_rows($r);
	if ($value > 0){}else{
		$value='0';
	}
	if ($white == ''){
		$white = $value;
	}else{
		$white .= ','.$value;
	}
}
//pull DRAPER files
$i=0;
while ($i < 12){$i++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT packet_id FROM ps_packets WHERE date_received LIKE '%2009-$i2%' AND attorneys_id='21'");
	$value=mysql_num_rows($r);
	if ($value > 0){}else{
		$value='0';
	}
	if ($draper == ''){
		$draper = $value;
	}else{
		$draper .= ','.$value;
	}
}
$i=0;
while ($i < 12){$i++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT packet_id FROM ps_packets WHERE date_received LIKE '%2010-$i2%' AND attorneys_id='21'");
	$value=mysql_num_rows($r);
	if ($value > 0){}else{
		$value='0';
	}
	if ($draper == ''){
		$draper = $value;
	}else{
		$draper .= ','.$value;
	}
}
//pull OTHER files
$i=0;
while ($i < 12){$i++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT packet_id FROM ps_packets WHERE date_received LIKE '%2009-$i2%' AND attorneys_id <> '1' AND attorneys_id <> '3' AND attorneys_id <> '21'");
	$value=mysql_num_rows($r);
	if ($value > 0){}else{
		$value='0';
	}
	if ($other == ''){
		$other = $value;
	}else{
		$other .= ','.$value;
	}
}
$i=0;
while ($i < 12){$i++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT packet_id FROM ps_packets WHERE date_received LIKE '%2010-$i2%' AND  AND attorneys_id <> '1' AND attorneys_id <> '3' AND attorneys_id <> '21'");
	$value=mysql_num_rows($r);
	if ($value > 0){}else{
		$value='0';
	}
	if ($other == ''){
		$other = $value;
	}else{
		$other .= ','.$value;
	}
}
$z1=number_format($z/5,0);
$z2=number_format($z1*2,0);
$z3=number_format($z1*3,0);
$z4=number_format($z1*4,0);
/*echo "<table><tr>";
echo "<td></td>".str_replace('|','</td><td>',$src2).'</td></tr>';
echo "<td>ALL:</td><td>".str_replace(',','</td><td>',$src).'</td></tr>';
echo "<tr><td>BURSON:</td><td>".str_replace(',','</td><td>',$burson).'</td></tr>';
echo "<tr><td>WHITE:</td><td>".str_replace(',','</td><td>',$white).'</td></tr>';
echo "<tr><td>DRAPER:</td><td>".str_replace(',','</td><td>',$draper).'</td></tr>';
echo "<tr><td>OTHER:</td><td>".str_replace(',','</td><td>',$other).'</td>';
echo "</tr></table>";* /
$src="http://chart.apis.google.com/chart?cht=lc&chs=1000x300&chd=t:".$src."|".$burson."|".$white."|".$draper."|".$other."&chxl=0:".$src2."|1:|0|$z1|$z2|$z3|$z4|$z&chtt=Foreclosure Files Received 2009-2010&chdl=All Files|Burson|White|Draper|Others&chco=FF0000,00FF00,0000FF,800080,FF8040&chls=1,1,0|1,1,0|1,1,0|1,1,0|1,1,0";
$rest="&chxt=x,y&chds=0,".$z."&chxtc=0,10|1,-980&chxs=0,000000,10|1,000000,10,-1,lt,333333&chm=t$z$zzz,000000,0,$zz,12";
?>
<img src="<?=$src.$rest?>" width="100%">
*/
}else{
echo "fuck off, secure file";
}
?>