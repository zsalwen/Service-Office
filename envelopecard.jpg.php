<?
session_start();
include 'common.php';
$canvas = imagecreate( 380, 800);
$_SESSION[pass]=$_GET[line2];
$src = "http://service.mdwestserve.com/barcode.php";
$insert = imagecreatefrompng($src);
imagecolortransparent($insert,imagecolorat($insert,0,0));
$white = imagecolorallocate( $canvas, 255, 255, 255 );
$black = imagecolorallocate( $canvas, 0, 0, 0 );
$font = "/fonts/OCRA.ttf";
$font2 = "/fonts/verdana.ttf";
$font3 = "/fonts/ATOMICCLOCKRADIO.TTF";
$size = "12";
$size2 = "22";
$size3 = "10";
$notice1 = "IMPORTANT NOTICE TO ALL OCCUPANTS:";
if ($_GET[svc] == 'EV'){
	$notice2 .= "EVICTION ";
}else{
	$notice2 .= "FORECLOSURE ";
}
$notice2 .= "INFORMATION ENCLOSED.";
$notice3 = "OPEN IMMEDIATELY.";

//return address
$line1 = "MD West Serve"; 
//$line4 = 'HTTP://MDWestServe.com';
$line2 = '300 E JOPPA RD STE 1102';
$line3 = 'TOWSON MD  21286-3012 ';
imageTTFText( $canvas, $size3, 270, 360, 0, $black, $font2, $line1);
imageTTFText( $canvas, $size3, 270, 345, 0, $black, $font2, $line2);
imageTTFText( $canvas, $size3, 270, 330, 0, $black, $font2, $line3);
$cord = "$clientFile".'X';
//main label
imageTTFText( $canvas, $size, 270, 220, 300, $black, $font, "ALL OCCUPANTS" );
imageTTFText( $canvas, $size, 270, 200, 300, $black, $font, strtoupper($_GET[line1]) );
imageTTFText( $canvas, $size, 270, 180, 300, $black, $font, strtoupper($_GET[csz]) );
imageTTFText( $canvas, $size2, 270, 142, 50, $black, $font3, strtoupper($notice1) );
imageTTFText( $canvas, $size2, 270, 112, 70, $black, $font3, strtoupper($notice2) );
imageTTFText( $canvas, $size2, 270, 80, 240, $black, $font3, strtoupper($notice3) );
header("Content-type: image/png"); 
$insert_x = imagesx($insert); 
$insert_y = imagesy($insert); 
//imagecopymerge($canvas,$insert,0,0,0,0,$insert_x,$insert_y,100);
imagepng($canvas);
?>
