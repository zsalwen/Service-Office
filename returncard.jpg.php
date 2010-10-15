<?
include 'common.php';

$canvas = imagecreate( 400, 100);
$white = imagecolorallocate( $canvas, 255, 255, 255 );
$black = imagecolorallocate( $canvas, 0, 0, 0 );
$font = "/fonts/OCRA.ttf";
$font2 = "/fonts/39251.TTF";
$size = "12";
$size2 = "16";
$size3 = "28";
if (strlen($_GET[line1]) > 33 && !$_GET[line2]){
	$line1 = substr($_GET[line1],0,32);
	$line2 = substr($_GET[line1],32);
}else{
	$line1 = $_GET[line1];
	$line2 = $_GET[line2];
}
if (strpos(strtoupper($_GET[csz]),'AKA')){
	$explode=explode(", MD",$_GET[csz]);
	$explode2=explode(" AKA ",$explode[0]);
	$explode[0]=$explode2[0];
	$csz = implode(", MD",$explode);
}else{
	$csz=$_GET[csz];
}
if ($_GET[line1]){
//imageTTFText( $canvas, $size2, 0, 10, 20, $black, $font, 'MDWS: '.$_GET[cord] );
imageTTFText( $canvas, $size, 0, 8, 20, $black, $font, $_GET[name] );
imageTTFText( $canvas, $size, 0, 8, 40, $black, $font, $line1 );
imageTTFText( $canvas, $size, 0, 8, 60, $black, $font, $line2 );
imageTTFText( $canvas, $size, 0, 8, 80, $black, $font, $csz );
//imageTTFText( $canvas, $size3, 0, 10, 65, $black, $font2, strtoupper($_GET[cord]).'X' );
}
header("Content-type: image/jpeg"); 
imagejpeg( $canvas, NULL, 100  );
?>
 