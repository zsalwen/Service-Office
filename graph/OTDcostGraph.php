<?
mysql_connect();
mysql_select_db('core');
include "functions.php";
ini_set("memory_limit","24M");
$year=$_GET[year];
$attid=$_GET[attid];
$month=0;

//monthly burn rate (not including postage, which is calculated within Service-Web-Service/cost.php
$burn=45186.24;
$curYear=date('Y');
$year=2008;
$inc=0;
$a=0;
$z=0;
$zi=0;
while ($year <= $curYear){
	//echo "<h1>$year</h1>";
	$month=0;
	$yr=substr($year,-2);
	if ($year != $curYear){
		$topMo=12;
	}else{
		$topMo=date('n');
	}
	while($month < $topMo){$month++;$zi++;$inc++;
		$count = $month;
		$month = leading_zeros($month, '2') ;

		$csv = getPage("http://data.mdwestserve.com/cost.php?month=$month&year=$year", 'MDWS GRAPH', '10', '');
		$value = explode(',',$csv);
		//echo "<fieldset><legend>$month-$year</legend>$csv</fieldset>";
		$i=0;
		while ($i < 4){
			if (!is_numeric($value["$i"])){$value["$i"]=0;}
			$i++;
		}
		$i=0;
		while ($i < 4){	
			if($value["$i"] > $z){
				$z=$value["$i"];
				$zPos=$zi-1;
				$zMonth="-".monthConvert($month)." $yr";
				$zSet=0;
			}
			$i++;
		}
		$i=0;
		while ($i < 4){	
			if($value["$i"] < $a){
				$a=$value["$i"];
				$aPos=$zi-1;
				$aMonth="-".monthConvert($month)." $yr";
				$aSet=0;
			}
			$i++;
		}
		//if ($value[4] != 0){
		$total["$count"] = $value[4] - $burn ;
		//}
		/*if($total["$count"] > $z){
				$z=$value["$i"];
				$zPos=$zi-1;
				$zMonth="-".monthConvert($month)." $yr";
				$zSet=0;
			}
		if ($total["$count"] < $a){
			$a=$value["$i"];
			$aPos=$zi-1;
			$aMonth="-".monthConvert($month)." $yr";
			$aSet=0;
		}*/
		if ($clientPaid == ''){
			$clientPaid = $value[0];
		}else{
			$clientPaid .= ",".$value[0];
		}
		//$mainPaidIn = $mainPaidIn + $value[0];
		if ($balanceDue == ''){
			$balanceDue = $value[1];
		}else{
			$balanceDue .= ",".$value[1];
		}
		//$mainOwed = $mainOwed + $value[1];
		if ($contractorPaid == ''){
			$contractorPaid = $value[2];
		}else{
			$contractorPaid .= ",".$value[2];
		}
		//$mainPaidOut = $mainPaidOut + $value[2];
		if ($liveMargin == ''){
			$liveMargin = $value[3];
		}else{
			$liveMargin .= ",".$value[3];
		}
		//$mainLiveMargin = $mainLiveMargin + $value[3];
		if ($estMargin == ''){
			$estMargin = $value[4];
		}else{
			$estMargin .= ",".$value[4];
		}
		//$mainMargin = $mainMargin + $value[4];
		$labels .= "|$month.$yr";
		//$counter=0;
		//while($counter < count($total)){$counter++;
		$totalList .= "|".number_format($total["$count"],0);
		$js .= '
		data.addRow(["'.$month.'/'.$yr.'",'.$value[0].','.$value[1].','.$value[2].','.$value[3].','.$value[4].','.number_format($total["$count"],0,'','').']);';
		//}
	}
	$year++;
}

/*echo "<table border='1' style='border-collapse:collapse;'><tr>";
echo "<td>LABELS:</td>".str_replace('|','</td><td>',$labels).'</td></tr>';
echo "<td>CLIENT PAID:</td><td>".str_replace(',','</td><td>',$clientPaid).'</td></tr>';
echo "<tr><td>BALANCE DUE:</td><td>".str_replace(',','</td><td>',$balanceDue).'</td></tr>';
echo "<tr><td>CONTRACTOR PAID:</td><td>".str_replace(',','</td><td>',$contractorPaid).'</td></tr>';
echo "<tr><td>LIVE MARGIN:</td><td>".str_replace(',','</td><td>',$liveMargin).'</td></tr>';
echo "<tr><td>EST. MARGIN:</td><td>".str_replace(',','</td><td>',$estMargin).'</td></tr>';
echo "<tr><td>TOTAL:</td><td>".str_replace('|','</td><td>',substr($totalList,1)).'</td></tr>';
echo "</table>";*/
$za=(($a*-1)+$z)/5;
//$zb is the vertical percentage where the zero marker should go on the y-axis
$zb=(($a*-100)+$z)/($z+($a*-1));
$zb2=$zb/100;
$z1=$a+$za;
$z2=$z1+$za;
$z3=$z2+$za;
$z4=$z3+$za;
if (!$_GET[noLegend]){
	$legend="&chdl=Client Paid|Balance Due|Contractor Paid|Live Margin|Est. Margin&chco=FF0000,00FF00,0000FF,800080,FF8040";
}
$src="http://0.chart.apis.google.com/chart?cht=lc&chs=900x333&chd=t:".$clientPaid."|".$balanceDue."|".$contractorPaid."|".$liveMargin."|".$estMargin."&chxl=0:".$labels."|1:|$a|0|$z1|$z2|$z3|$z4|$z|2:|$totalList&chtt=Costs: 2008-$curYear|$legend&chxt=x,y,x&chds=$a,$z&chxtc=0,10|1,-980&chxp=1,0,$zb,20,40,60,80,100&chxs=1,000000,6|0,000000,6|2,000000,6&chls=0.5,1,0|0.5,1,0|0.5,1,0|0.5,1,0|0.5,1,0&chm=h,CCBB00,0,$zb2,1&chm=f$z$zMonth,000000,$zSet,$zPos,12|f$a$aMonth,000000,$aSet,$aPos,12";
//$rest="&chxt=x,y&chds=0,".$z."&chxtc=0,10|1,-980&chxs=0,000000,10|1,000000,10,-1,lt,333333&chm=f$z,000000,0,$zPos,15";
?>
<!--------------
<img src="<?=$src?>" width="100%">
----------->
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
        data.addColumn('number', 'Client Paid');
        data.addColumn('number', 'Balance Due');
        data.addColumn('number', 'Contractor Paid');
        data.addColumn('number', 'Live Margin');
        data.addColumn('number', 'Estimated Margin');
        data.addColumn('number', 'Estimated Remainder');
       <?=$js?>
        // Create and draw the visualization.
        new google.visualization.LineChart(document.getElementById('visualization')).
            draw(data, {curveType: "function",
                        width: 1250, height: 550,
                        vAxis: {maxValue: <?=$z?>}, title: 'Costs: 2008-<?=$curYear?>',
						 hAxis: {title: 'Date', titleTextStyle: {color: '#FF0000', fontSize:'18'} }
						 chartArea:{left:20,top:0,width:"90%",height:"90%"}
						  }
                );
      }
      

      google.setOnLoadCallback(drawVisualization);
    </script>
    <div id="visualization" style="width: 500px; height: 400px;"></div>