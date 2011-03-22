<?
mysql_connect();
mysql_select_db('core');
include "functions.php";
//z will be the largest number encountered
$z=0;
$zi=0;
$year=2008;
$curYear=date('Y');
$inc=0;
while ($year <= $curYear){
	$yr=substr($year,-2);
	if ($year != $curYear){
		$topMo=12;
	}else{
		$topMo=date('n');
	}
	$i=0;
	$received='';
	while ($i < $topMo){$i++;$zi++;$inc++;
		if ($i < 10){
			$i2='0'.$i;
		}else{
			$i2=$i;
		}
// the is the all files line
		$r=mysql_query("SELECT id FROM packet WHERE date_received LIKE '%$year-$i2%'");
		$received["$i"]=mysql_num_rows($r);
		if ($received["$i"] > 0){}else{
			$received["$i"]='0';
		}
		if ($received["$i"] > $z){
			$z=$received["$i"];
			$zz=$zi-1;
			$zzz="-".monthConvert($i2)." '$yr";
		}
		if ($src == ''){
			$src = $received["$i"];
		}else{
			$src .= ','.$received["$i"];
		}
		$src2 .= '|'.monthConvert($i2)." $yr";
		//this is the presale line (was BURSON files)
		$r=mysql_query("SELECT id FROM packet WHERE date_received LIKE '%$year-$i2%' AND product_id='1'");
		$value1=mysql_num_rows($r);
		if ($value1 > 0){}else{
			$value1='0';
		}
		if ($burson == ''){
			$burson = $value1;
		}else{
			$burson .= ','.$value1;
		}
		//this is the eviction line (was WHITE files)
		$r=mysql_query("SELECT id FROM packet WHERE date_received LIKE '%$year-$i2%' AND product_id='2'");
		$value2=mysql_num_rows($r);
		if ($value2 > 0){}else{
			$value2='0';
		}
		if ($white == ''){
			$white = $value2;
		}else{
			$white .= ','.$value2;
		}
		//this is the standard line (was BGW files)
		$r=mysql_query("SELECT id FROM packet WHERE date_received LIKE '%$year-$i2%' AND product_id='3'");
		$value3=mysql_num_rows($r);
		if ($value3 > 0){}else{
			$value3='0';
		}
		if ($bgw == ''){
			$bgw = $value3;
		}else{
			$bgw .= ','.$value3;
		}
		//this is the mail only line (was OTHER files)
		$r=mysql_query("SELECT id FROM packet WHERE date_received LIKE '%$year-$i2%' AND product_id = '4'");
		$value4=mysql_num_rows($r);
		if ($value4 > 0){}else{
			$value4='0';
		}
		if ($other == ''){
			$other = $value4;
		}else{
			$other .= ','.$value4;
		}
		$js .= '
		data.addRow(["'.monthConvert($i2).'/'.$yr.'",'.$received["$i"].' ,'.$value1.' ,'.$value2.' ,'.$value3.','.$value4.']);';
	}
	$year++;
}

$z1=number_format($z/5,0);
$z2=number_format($z1*2,0);
$z3=number_format($z1*3,0);
$z4=number_format($z1*4,0);
/*echo "<table><tr>";
echo "<td></td>".str_replace('|','</td><td>',$src2).'</td></tr>';
echo "<td>ALL:</td><td>".str_replace(',','</td><td>',$src).'</td></tr>';
echo "<tr><td>PRESALE:</td><td>".str_replace(',','</td><td>',$burson).'</td></tr>';
echo "<tr><td>EVICTION:</td><td>".str_replace(',','</td><td>',$white).'</td></tr>';
echo "<tr><td>STANDARD:</td><td>".str_replace(',','</td><td>',$bgw).'</td></tr>';
echo "<tr><td>MAILONLY:</td><td>".str_replace(',','</td><td>',$other).'</td>';
echo "</tr></table>";*/
$src="http://1.chart.apis.google.com/chart?cht=lc&chs=1000x300&chd=t:".$src."|".$burson."|".$white."|".$bgw."|".$other."&chxl=0:".$src2."|1:|0|$z1|$z2|$z3|$z4|$z&chtt=All Service Files Received 2008-$curYear&chdl=All Files|Burson|White|BGW|Others&chco=FF0000,00FF00,0000FF,800080,FF8040&chls=1,1,0|1,1,0|1,1,0|1,1,0|1,1,0";
$rest="&chxt=x,y&chds=0,".$z."&chxtc=0,10|1,-980&chxs=0,000000,7|1,000000,10,-1,lt,333333&chm=t$z$zzz,000000,0,$zz,10";
?>
<!-------------
<img src="<?=$src.$rest?>" width="100%">
---->
<!--
You are free to copy and use this sample in accordance with the terms of the
Apache license (http://www.apache.org/licenses/LICENSE-2.0.html)
-->

    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">
      function drawVisualization() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Date');
        data.addColumn('number', 'All Products');
        data.addColumn('number', 'Presale');
        data.addColumn('number', 'Eviction');
        data.addColumn('number', 'Standard');
        data.addColumn('number', 'Mail Only');
       <?=$js?>
        // Create and draw the visualization.
        new google.visualization.LineChart(document.getElementById('visualization')).
            draw(data, {curveType: "function",
                        width: 1200, height: 480, backgroundColor: '#99AACC',
                        vAxis: {maxValue: <?=$z?>}, title: 'All Service Files Received: 2008-<?=$curYear?>',
						 hAxis: {title: 'Date', titleTextStyle: {color: '#FF0000', fontSize:'18'} }
						  }
                );
      }
      

      google.setOnLoadCallback(drawVisualization);
    </script>
    <div id="visualization" style="width: 100%; height: 100%;"></div>