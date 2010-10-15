<?
include 'common.php';
$canvas = imagecreate( 150, 600);
$white = imagecolorallocate( $canvas, 255, 255, 255 );
$black = imagecolorallocate( $canvas, 0, 0, 0 );
$font = "/fonts/OCRA.ttf";
$size = "12";
$size2 = "8";
imageTTFText( $canvas, $size, 270, 130, 10, $black, $font, strtoupper($_GET[name]) );
imageTTFText( $canvas, $size, 270, 110, 10, $black, $font, strtoupper($_GET[line1]) );
imageTTFText( $canvas, $size, 270, 90, 10, $black, $font, strtoupper($_GET[csz]) );
imageTTFText( $canvas, $size2, 270, 70, 10, $black, $font, 'Logic No. '.$_GET[cord] );
imageTTFText( $canvas, $size, 270, 88, 285, $black, $font, 'x' );
//imageTTFText( $canvas, $size, 270, 75, 368, $black, $font, 'x' );
imageTTFText( $canvas, $size, 270, 10, 150, $black, $font, $_GET[art] );
header("Content-type: image/jpeg"); 
imagejpeg( $canvas, NULL, 100 );
?>
