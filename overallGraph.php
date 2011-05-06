<?
session_start();
mysql_connect();
mysql_select_db('core');
$r=@mysql_query("select * from overallGraph order by id desc limit 0,30");
$array1=array();
$array2=array();
?>
   <script language="javascript" src="http://www.google.com/jsapi"></script>
<style>
body { padding:0px; margin:0px; }
td { text-align:center; padding:0px; }
table { border-collapse: collapse; }
</style>
<table border="1" width="100%">
<tr>
<td bgcolor="AFEEEE"><b>Date</b></td>
<td bgcolor="AFEEEE"><b>Received to Dispatch</b></td>
<td bgcolor="AFEEEE"><b>Dispatch to Close</b></td>
<td bgcolor="5DFC0A"><b>Webservice Queue</b></td>
<td bgcolor="FF8000"><b>New OTD</b></td>
<td bgcolor="FF8000"><b>Dispatch OTD</b></td>
<td bgcolor="FF8000"><b>Active OTD</b></td>
<td bgcolor="FF8000"><b>Quality Control OTD</b></td>
<td bgcolor="FF8000"><b>Mailroom OTD</b></td>
<td bgcolor="FF8000"><b>Blackhole OTD</b></td>
<td bgcolor="FBEC5D"><b>New EV</b></td>
<td bgcolor="FBEC5D"><b>Dispatch EV</b></td>
<td bgcolor="FBEC5D"><b>Active EV</b></td>
<td bgcolor="FBEC5D"><b>Quality Control EV</b></td>
<td bgcolor="FBEC5D"><b>Mailroom EV</b></td>
<td bgcolor="FBEC5D"><b>Blackhole EV</b></td>
<td bgcolor="EE8262"><b>New S</b></td>
<td bgcolor="EE8262"><b>Active S</b></td>
<td bgcolor="EE8262"><b>In Progress S</b></td>
<td bgcolor="EE8262"><b>S</b></td>
<td bgcolor="8EE5EE"><b>Missing Case Number</b></td>
<td bgcolor="8EE5EE"><b>Watchdog Searching</b></td>
<td bgcolor="8EE5EE"><b>Watchdog Found</b></td>
<td bgcolor="5DFC0A"><b>30 Day Volume</b></td>
</tr>
<?
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
$array1[$d[date]]=$d[dispatch];
$array2[$d[date]]=$d[closed];
$array3[$d[date]]=$d[otdQ];
?>
<tr>
<td bgcolor="AFEEEE"><?=$d[date]?></td>
<td bgcolor="AFEEEE"><?=$d[dispatch]?></td>
<td bgcolor="AFEEEE"><?=$d[closed]?></td>
<td bgcolor="5DFC0A"><?=$d[pre]?></td>
<td bgcolor="FF8000"><?=$d[otdN]?></td>
<td bgcolor="FF8000"><?=$d[otdD]?></td>
<td bgcolor="FF8000"><?=$d[otdA]?></td>
<td bgcolor="FF8000"><?=$d[otdQ]?></td>
<td bgcolor="FF8000"><?=$d[otdM]?></td>
<td bgcolor="FF8000"><?=$d[otdB]?></td>
<td bgcolor="FBEC5D"><?=$d[evN]?></td>
<td bgcolor="FBEC5D"><?=$d[evD]?></td>
<td bgcolor="FBEC5D"><?=$d[evA]?></td>
<td bgcolor="FBEC5D"><?=$d[evQ]?></td>
<td bgcolor="FBEC5D"><?=$d[evM]?></td>
<td bgcolor="FBEC5D"><?=$d[evB]?></td>
<td bgcolor="EE8262"><?=$d[sN]?></td>
<td bgcolor="EE8262"><?=$d[sA]?></td>
<td bgcolor="EE8262"><?=$d[sI]?></td>
<td bgcolor="EE8262"><?=$d[s]?></td>
<td bgcolor="8EE5EE"><?=$d[wa]?></td>
<td bgcolor="8EE5EE"><?=$d[wb]?></td>
<td bgcolor="8EE5EE"><?=$d[wc]?></td>
<td bgcolor="5DFC0A"><?=$d[vol]?></td>
</tr>
<?
}
mysql_close();
?>
</table>
<center><b>high,current,low</b></center>
<? 
function buildSub($sub,$i){
$sub = explode(',',$sub);
$z = $sub[0]; // high
$y = $sub[1]; // current
$x = $sub[2]; // low
$high = $z;
$current = $y - $x;
$low = ($z - $x) - ($y - $x); 
if( $z > $_SESSION[graphHigh] ){ $_SESSION[graphHigh] = $z; }
if( $x < $_SESSION[graphLow] ){ $_SESSION[graphLow] = $x; }
?>
          dataTable.setValue(<?=$i?>, 0, <?=$low;?>);
          dataTable.setValue(<?=$i?>, 1, <?=$current;?>);
          dataTable.setValue(<?=$i?>, 2, <?=$high;?>);
<?
}
function makeChart($name,$id,$i,$array,$days){ 
$_SESSION[graphHigh] = 0; 
$_SESSION[graphLow] = 0; 
?>
<script type="text/javascript">
      var queryString = '';
      var dataUrl = '';

      function onLoadCallback<?=$i;?>() {
        if (dataUrl.length > 0) {
          var query = new google.visualization.Query(dataUrl);
          query.setQuery(queryString);
          query.send(handleQueryResponse);
        } else {
          var dataTable = new google.visualization.DataTable();
          dataTable.addRows(<?=$days;?>);

          dataTable.addColumn('number');
          dataTable.addColumn('number');
          dataTable.addColumn('number');


<?
$i=0;
while($i<$days){
echo buildSub($array[date("Y-m-d", mktime(0, 0, 0, date("m"),date("d")-$i,date("Y")))],$i);
$i++;
}
?>


/*

     

          dataTable.setValue(3, 0, 95.00);
          dataTable.setValue(3, 1, 86.00);
          dataTable.setValue(3, 2, 53.00);

          dataTable.setValue(4, 0, 48.00);
          dataTable.setValue(4, 1, 73.00);
          dataTable.setValue(4, 2, 60.00);

          dataTable.setValue(5, 0, 49.00);
          dataTable.setValue(5, 1, 52.00);
          dataTable.setValue(5, 2, 48.00);

          dataTable.setValue(6, 0, 41.00);
          dataTable.setValue(6, 1, 58.00);
          dataTable.setValue(6, 2, 10.00);
*/
          draw<?=$i;?>(dataTable);
        }
      }

      function draw<?=$i;?>(dataTable) {
        var vis = new google.visualization.ImageChart(document.getElementById('<?=$id?>'));
        var options = {
          chf: 'bg,s,C2BDDD',
          chxl: '',
          chxp: '',
          chxr: '0,<?=$graphLow;?>,<?=$graphHigh;?>',
          chxs: '0,676767,10.5,0,l,676767',
          chxtc: '',
          chxt: 'y',
          chbh: 'a',
          chs: '300x225',
          cht: 'bvs',
          chco: 'FF0000,3072F3,00FF00',
          chd: 'e:rq4q6d80fEfpaj,WHtUp43NuyhclT,TQO2bziQmifNGZ',
          chdl: 'Low|Current|High',
          chdlp: 't',
          chtt: '<?=$name?>'
        };
        vis.draw(dataTable, options);
      }

      function handleQueryResponse(response) {
        if (response.isError()) {
          alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
          return;
        }
        draw<?=$i;?>(response.getDataTable());
      }

      google.load("visualization", "<?=$i;?>", {packages:["imagechart"]});
      google.setOnLoadCallback(onLoadCallback<?=$i;?>);

    </script>
<? } ?>



<table border="1" width="100%">
<tr>
<td><div id="chart1"></div><?=makeChart('Received to Dispatch','chart1',1,$array1);?></td>
<td><div id="chart2"></div><?=makeChart('Dispatch to Close','chart2',2,$array2);?></td>
<td><div id="chart3"></div><?=makeChart('OTD Quality Control','chart3',3,$array3);?></td>
</tr>
</table>



<pre>
<?php
print_r ($array1);
?>
</pre>
<pre>
<?php
print_r ($array2);
?>
</pre>
<pre>
<?php
print_r ($array3);
?>
</pre>
