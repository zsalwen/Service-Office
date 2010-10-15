<?
mysql_connect();
mysql_select_db('core');
include "functions.php";
$i=4;
//z will be the largest number encountered
$z=0;
$zi=0;
while ($i < 12){$i++;$zi++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT eviction_id FROM evictionPackets WHERE fileDate LIKE '%2008-$i2%'");
	$received08["$i"]=mysql_num_rows($r);
	if ($received08["$i"] > 0){}else{
		$received08["$i"]='0';
	}
	if ($received08["$i"] > $z){
		$z=$received08["$i"];
		$zz=$zi-1;
		$zzz="-".monthConvert($i2)." 08";
	}
	if ($src == ''){
		$src = $received08["$i"];
	}else{
		$src .= ','.$received08["$i"];
	}
	$src2 .= '|'.monthConvert($i2)." '08";
}
$i=0;
while ($i < 12){$i++;$zi++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT eviction_id FROM evictionPackets WHERE fileDate LIKE '%2009-$i2%'");
	$received09["$i"]=mysql_num_rows($r);
	if ($received09["$i"] > 0){}else{
		$received09["$i"]='0';
	}
	if ($received09["$i"] > $z){
		$z=$received09["$i"];
		$zz=$zi-1;
		$zzz="-".monthConvert($i2)." 09";
	}
	if ($src == ''){
		$src = $received09["$i"];
	}else{
		$src .= ','.$received09["$i"];
	}
	$src2 .= '|'.monthConvert($i2)." '09";
}
//pull BURSON files
$i=4;
while ($i < 12){$i++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT eviction_id FROM evictionPackets WHERE fileDate LIKE '%2008-$i2%' AND attorneys_id='1'");
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
	$r=mysql_query("SELECT eviction_id FROM evictionPackets WHERE fileDate LIKE '%2009-$i2%' AND attorneys_id='1'");
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
$i=4;
while ($i < 12){$i++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT eviction_id FROM evictionPackets WHERE fileDate LIKE '%2008-$i2%' AND attorneys_id='3'");
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
	$r=mysql_query("SELECT eviction_id FROM evictionPackets WHERE fileDate LIKE '%2009-$i2%' AND attorneys_id='3'");
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
$i=4;
while ($i < 12){$i++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT eviction_id FROM evictionPackets WHERE fileDate LIKE '%2008-$i2%' AND attorneys_id='21'");
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
	$r=mysql_query("SELECT eviction_id FROM evictionPackets WHERE fileDate LIKE '%2009-$i2%' AND attorneys_id='21'");
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
$i=4;
while ($i < 12){$i++;
	if ($i < 10){
		$i2='0'.$i;
	}else{
		$i2=$i;
	}
	$r=mysql_query("SELECT eviction_id FROM evictionPackets WHERE fileDate LIKE '%2008-$i2%' AND attorneys_id <> '1' AND attorneys_id <> '3' AND attorneys_id <> '21'");
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
	$r=mysql_query("SELECT eviction_id FROM evictionPackets WHERE fileDate LIKE '%2009-$i2%' AND  AND attorneys_id <> '1' AND attorneys_id <> '3' AND attorneys_id <> '21'");
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
echo "</tr></table>";*/
$src="http://chart.apis.google.com/chart?cht=lc&chs=1000x300&chd=t:".$src."|".$burson."|".$white."|".$draper."|".$other."&chxl=0:".$src2."|1:|0|$z1|$z2|$z3|$z4|$z&chtt=Evictions Filed 2008-2009&chdl=All Files|Burson|White|Draper|Others&chco=FF0000,00FF00,0000FF,800080,FF8040";
$markers="&chm=d,990000,0,-1,5|d,009900,1,-1,5|d,000099,2,-1,5|d,662266,3,-1,5|d,994400,4,-1,5|t$z$zzz,000000,0,$zz,13";
$rest="&chxt=x,y&chds=0,".$z."&chxtc=0,10|1,-980&chxs=0,000000,8|1,000000,10,-1,lt,333333";
?>
<img src="<?=$src.$rest.$markers?>" width="100%">