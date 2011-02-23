<?
mysql_close();
$headers = apache_request_headers(); 
$lb = $headers["X-Forwarded-Host"];
$mirror = $_SERVER['HTTP_HOST'];
?>
<center style="padding:0px;">Mysql Closed on <?=$mirror;?> from <?=$lb;?></center>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-9542421-2");
pageTracker._trackPageview();
} catch(err) {}</script>