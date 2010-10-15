<?
function duplicatePDF($fileA,$fileB){

	$fh = fopen($fileA, 'r');
	$fileAdata = fread($fh, 5);
	fclose($fh);
	
	$fh = fopen($fileB, 'r');
	$fileBdata = fread($fh, 5);
	fclose($fh);
	
	if ($fileAdata == $fileBdata){
		return "Exact Duplicate";
	}else{
		return "Missmatch";
	}
}

if ($_GET[a] && $_GET[b]){
	echo duplicatePDF($_GET[a],$_GET[b]);
}
?>