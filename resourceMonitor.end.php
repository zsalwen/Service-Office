<?
$mtimeResourceMonitorStart = microtime();
$mtimeResourceMonitorStart = explode (" ", $mtimeResourceMonitorStart);
$mtimeResourceMonitorStart = $mtimeResourceMonitorStart[1] + $mtimeResourceMonitorStart[0];
$tendResourceMonitorStart = $mtimeResourceMonitorStart;
$totaltimeResourceMonitorStart = ($tendResourceMonitorStart - $tstartResourceMonitorStart);
if ($totaltimeResourceMonitorStart > '1'){
$logResourceMonitorStart = '/logs/response.log';
resourceMonitorStartServerResponse(str_replace('/sandbox/','',$_SERVER["SCRIPT_FILENAME"]),number_format($totaltimeResourceMonitorStart,10),$_SERVER["QUERY_STRING"],$logResourceMonitorStart,$_GET[debug]);
}
?>