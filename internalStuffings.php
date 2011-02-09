<?
mysql_connect();
mysql_select_db('service');
function pageMaker($id,$matrix){
$r=@mysql_query("SELECT * FROM envelopeImage WHERE envID = '$id'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
ob_start();
?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<table style="page-break-after:always;">
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to1]))?></td>
	</tr>	
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to2]))?></td>
	</tr>	
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to3]))?></td>
	</tr>	
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=strtoupper($matrix)?>: REQUEST FOR FORECLOSURE MEDIATION</td>
	</tr>
</table>
<? 
$html = ob_get_clean();
return $html;
}
function flatMaker($id,$matrix){
$r=@mysql_query("SELECT * FROM envelopeImage WHERE envID = '$id'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
ob_start();
?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<div>
<h1>Instructions</h1>
<ol>
<li>Cut the label out below along the dotteed line.</li>
<li>Affix label to the front of the green 9x12 'flat' envelope with clear tape.</li>
<li>Recycle the remaining instruction paper.</li>
</ol>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<table style="page-break-after:always; border:dashed;" width="100%">
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to1]))?></td>
	</tr>	
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to2]))?></td>
	</tr>	
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to3]))?></td>
	</tr>	
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=strtoupper($matrix)?>: REQUEST FOR FORECLOSURE MEDIATION</td>
	</tr>
</table>
<? 
$html = ob_get_clean();
return $html;
}

// ok based on packet number



// and core


$page = pageMaker(1,'O12345-1A');
$page .= pageMaker(2,'O12345-1A');
$page .= flatMaker(3,'O12345-1A');


require_once("/thirdParty/dompdf-0.5.1/dompdf_config.inc.php");
$old_limit = ini_set("memory_limit", "16M");
$dompdf = new DOMPDF();
$dompdf->load_html($page);
$dompdf->set_paper('letter', 'portrait');
$dompdf->render();
$dompdf->stream('envelopes-'.$_GET['matrix'].".pdf");
?>

