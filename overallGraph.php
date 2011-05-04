<?
mysql_connect();
mysql_select_db('core');
$r=@mysql_query("select * from overallGraph order by id desc limit 0,30");
?>
   <script language="javascript" src="http://www.google.com/jsapi"></script>
<style>
body { padding:0px; margin:0px; }
td { text-align:center; padding:2px; }
table { border-collapse: collapse; }
</style>
<table border="1" width="100%">
<tr>
<td bgcolor="AFEEEE"><b>Date</b></td>
<td bgcolor="AFEEEE"><b>Received to Dispatch</b></td>
<td bgcolor="AFEEEE"><b>Dispatch to Close</b></td>
<td bgcolor="5DFC0A"><b>Webservice Queue</b></td>
<td bgcolor="FF8000"><b>New OTD</b></td>
<td bgcolor="FF8000"><b>Active OTD</b></td>
<td bgcolor="FF8000"><b>Quality Control OTD</b></td>
<td bgcolor="FF8000"><b>Mailroom OTD</b></td>
<td bgcolor="FF8000"><b>Blackhole OTD</b></td>
<td bgcolor="FBEC5D"><b>New EV</b></td>
<td bgcolor="FBEC5D"><b>Active EV</b></td>
<td bgcolor="FBEC5D"><b>Blankhole EV</b></td>
<td bgcolor="EE8262"><b>New S</b></td>
<td bgcolor="EE8262"><b>Active S</b></td>
<td bgcolor="EE8262"><b>In Progress S</b></td>
<td bgcolor="8EE5EE"><b>Watchdog Active</b></td>
<td bgcolor="8EE5EE"><b>Watchdog Blackhole</b></td>
<td bgcolor="5DFC0A"><b>30 Day Volume</b></td>
</tr>
<?
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
?>
<tr>
<td bgcolor="AFEEEE"><?=$d[date]?></td>
<td bgcolor="AFEEEE"><?=$d[dispatch]?></td>
<td bgcolor="AFEEEE"><?=$d[closed]?></td>
<td bgcolor="5DFC0A"><?=$d[pre]?></td>
<td bgcolor="FF8000"><?=$d[otdN]?></td>
<td bgcolor="FF8000"><?=$d[otdA]?></td>
<td bgcolor="FF8000"><?=$d[otdQ]?></td>
<td bgcolor="FF8000"><?=$d[otdM]?></td>
<td bgcolor="FF8000"><?=$d[otdB]?></td>
<td bgcolor="FBEC5D"><?=$d[evN]?></td>
<td bgcolor="FBEC5D"><?=$d[evA]?></td>
<td bgcolor="FBEC5D"><?=$d[evB]?></td>
<td bgcolor="EE8262"><?=$d[sN]?></td>
<td bgcolor="EE8262"><?=$d[sA]?></td>
<td bgcolor="EE8262"><?=$d[sI]?></td>
<td bgcolor="8EE5EE"><?=$d[wa]?></td>
<td bgcolor="8EE5EE"><?=$d[wb]?></td>
<td bgcolor="5DFC0A"><?=$d[vol]?></td>
</tr>
<?
}
mysql_close();
?>
</table>
<center><b>high,current,low</b></center>
<hr>




<? function makeChart($name,$id){ ?>


   <div id="<?=$id?>"></div>

   <script type="text/javascript">
      var queryString = '';
      var dataUrl = '';

      function onLoadCallback() {
        if (dataUrl.length > 0) {
          var query = new google.visualization.Query(dataUrl);
          query.setQuery(queryString);
          query.send(handleQueryResponse);
        } else {
          var dataTable = new google.visualization.DataTable();
          dataTable.addRows(7);

          dataTable.addColumn('number');
          dataTable.addColumn('number');
          dataTable.addColumn('number');
          dataTable.setValue(0, 0, 68.24072192621668);
          dataTable.setValue(0, 1, 34.56596279744238);
          dataTable.setValue(0, 2, 30.098);
          dataTable.setValue(1, 0, 88.56959906724578);
          dataTable.setValue(1, 1, 70.82607732878787);
          dataTable.setValue(1, 2, 23.223);
          dataTable.setValue(2, 0, 91.36680313467521);
          dataTable.setValue(2, 1, 65.46853881413088);
          dataTable.setValue(2, 2, 43.467);
          dataTable.setValue(3, 0, 95.05014894731983);
          dataTable.setValue(3, 1, 86.29010404393469);
          dataTable.setValue(3, 2, 53.548);
          dataTable.setValue(4, 0, 48.556824963465665);
          dataTable.setValue(4, 1, 73.13114860769234);
          dataTable.setValue(4, 2, 60.24);
          dataTable.setValue(5, 0, 49.45816933251623);
          dataTable.setValue(5, 1, 52.25935572937881);
          dataTable.setValue(5, 2, 48.786);
          dataTable.setValue(6, 0, 41.50393106825148);
          dataTable.setValue(6, 1, 58.291845810651964);
          dataTable.setValue(6, 2, 10);

          draw(dataTable);
        }
      }

      function draw(dataTable) {
        var vis = new google.visualization.ImageChart(document.getElementById('<?=$id?>'));
        var options = {
          chf: 'bg,s,C2BDDD',
          chxl: '',
          chxp: '',
          chxr: '0,0,90',
          chxs: '0,676767,10.5,0,l,676767',
          chxtc: '',
          chxt: 'y',
          chbh: 'a',
          chs: '300x225',
          cht: 'bvs',
          chco: 'FF0000,00FF00,3072F3',
          chd: 'e:rq4q6d80fEfpaj,WHtUp43NuyhclT,TQO2bziQmifNGZ',
          chdl: 'High+Value|Current+Value|Low+Value',
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
        draw(response.getDataTable());
      }

      google.load("visualization", "1", {packages:["imagechart"]});
      google.setOnLoadCallback(onLoadCallback);

    </script>

<? } ?>

<table border="1" width="100%">
<tr>
<td><?=makeChart('Received to Dispatch','chart1');?></td>
<td><?=makeChart('Dispatch to Close','chart2');?></td>
</tr>
</table>


