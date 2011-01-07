<?
include 'common.php';
$canvas = imagecreate( 210, 600);
$white = imagecolorallocate( $canvas, 255, 255, 255 );
$black = imagecolorallocate( $canvas, 0, 0, 0 );
$font = "/fonts/verdana.ttf";
$size = "12";
$total = number_format($_GET[cost]+2.8+2.35,2);
if (!$_GET[noCost]){
imageTTFText( $canvas, $size, 270, 195, 85, $black, $font, '$'.$_GET[cost] );
imageTTFText( $canvas, $size, 270, 170, 85, $black, $font, '$2.80' );
imageTTFText( $canvas, $size, 270, 145, 85, $black, $font, '$2.35' );
imageTTFText( $canvas, $size, 270, 120, 85, $black, $font, '$'.$total );
}
//imageTTFText( $canvas, $size, 270, 100, 85, $black, $font, '$4.90' );
imageTTFText( $canvas, $size, 270, 68, 10, $black, $font, $_GET[cord].': '.$_GET[name] );
imageTTFText( $canvas, $size, 270, 48, 10, $black, $font, $_GET[line1].', '.$_GET[line2]);
imageTTFText( $canvas, $size, 270, 25, 10, $black, $font, $_GET[csz] );
header("Content-type: image/jpeg"); 
imagejpeg( $canvas );
?>
