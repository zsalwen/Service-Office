<?
session_start();
include 'common.php';
$canvas = imagecreate( 380, 800);
$_SESSION[pass]=$_GET[line2];
$src = "http://mdwestserve.com/ps/barcode.php";
$insert = imagecreatefrompng($src);
imagecolortransparent($insert,imagecolorat($insert,0,0));
$white = imagecolorallocate( $canvas, 255, 255, 255 );
$black = imagecolorallocate( $canvas, 0, 0, 0 );
$font = "/fonts/OCRA.ttf";
$font2 = "/fonts/verdana.ttf";
$font3 = "/fonts/ATOMICCLOCKRADIO.TTF";
$size = "12";
$size2 = "22";
$notice1 = "REQUEST FOR FORECLOSURE MEDIATION";

$notice4 = "ATTN: MARYLAND PRESALE";

//return address
$line1 = "_______________________"; 

imageTTFText( $canvas, $size, 270, 360, 0, $black, $font2, $line1);
imageTTFText( $canvas, $size, 270, 340, 0, $black, $font2, $line1);
imageTTFText( $canvas, $size, 270, 320, 0, $black, $font2, $line1);
$cord = "$clientFile".'X';
//main label
if (stripos($_GET[line1],"-")){
	$line1=explode('-',$_GET[line1]);
	$line1a=$line1[0];
	$line1b=$line1[1];
}elseif($_GET[line1] == "CLERK OF THE CIRCUIT COURT FOR PRINCE GEORGE'S COUNTY"){
	$line1a="CLERK OF THE CIRCUIT COURT";
	$line1b="FOR PRINCE GEORGE'S COUNTY";
}else{
	$line1b=$_GET[line1];
}
//only display "ATTN: MARYLAND PRESALE" for letters to attorney

if ($_GET[lossMit] != 'PRELIMINARY'){
	if ($_GET[client] != ''){
		imageTTFText( $canvas, $size, 270, 240, 240, $black, $font, strtoupper($notice4) );
	}
	imageTTFText( $canvas, $size2, 270, 190, 60, $black, $font3, strtoupper($notice1) );
}elseif ($_GET[client] != ''){
	imageTTFText( $canvas, $size, 270, 190, 240, $black, $font, strtoupper($notice4) );
}
imageTTFText( $canvas, $size, 270, 170, 240, $black, $font, strtoupper($line1a) );
imageTTFText( $canvas, $size, 270, 150, 240, $black, $font, strtoupper($line1b) );
imageTTFText( $canvas, $size, 270, 130, 240, $black, $font, strtoupper($_GET[line2]) );
imageTTFText( $canvas, $size, 270, 110, 240, $black, $font, strtoupper($_GET[csz]) );
//imageTTFText( $canvas, $size2, 270, 80, 240, $black, $font3, strtoupper($notice3) );
header("Content-type: image/png"); 
$insert_x = imagesx($insert); 
$insert_y = imagesy($insert); 
//imagecopymerge($canvas,$insert,0,0,0,0,$insert_x,$insert_y,100);
imagepng($canvas);
?>
