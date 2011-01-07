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
$font = "/fonts/calibri.ttf";
$font2 = "/fonts/times.ttf";
$font3 = "/fonts/arial.ttf";
$size = "12";
$size2 = "22";
$notice1 = "REQUEST FOR FORECLOSURE MEDIATION";

$line1="";
$line3="";
//return address
$return1 = "_______________________"; 

imageTTFText( $canvas, $size, 270, 360, 0, $black, $font2, $return1);
imageTTFText( $canvas, $size, 270, 340, 0, $black, $font2, $return1);
imageTTFText( $canvas, $size, 270, 320, 0, $black, $font2, $return1);
$cord = "$clientFile".'X';
//main label
if (stripos($_GET[line1],"-")){
	$explode1=explode('-',$_GET[line1]);
	$line1=$explode1[0];
	$line2=$explode1[1];
}elseif (stripos($_GET[line2],"-")){
	$explode2=explode('-',$_GET[line2]);
	$line1=$_GET[line1];
	$line2=$explode2[0];
	$line3=$explode2[1];
}elseif($_GET[line1] == "CLERK OF THE CIRCUIT COURT FOR PRINCE GEORGE'S COUNTY"){
	$line1="CLERK OF THE CIRCUIT COURT";
	$line2="FOR PRINCE GEORGE'S COUNTY";
}else{
	$line2=$_GET[line1];
}
if ($line3 == ""){
	$line3=$_GET[line2];
}
//only display "ATTN: MARYLAND PRESALE" for letters to attorney

if ($_GET[lossMit] != 'PRELIMINARY'){
	if (!$_GET[client]){
		imageTTFText( $canvas, $size2, 270, 80, 10, $black, $font3, strtoupper($notice1) );
	}
}
imageTTFText( $canvas, $size, 270, 170, 240, $black, $font3, strtoupper($line1) );
imageTTFText( $canvas, $size, 270, 150, 240, $black, $font3, strtoupper($line2) );
imageTTFText( $canvas, $size, 270, 130, 240, $black, $font3, strtoupper($line3) );
imageTTFText( $canvas, $size, 270, 110, 240, $black, $font3, strtoupper($_GET[csz]) );
//imageTTFText( $canvas, $size2, 270, 80, 240, $black, $font3, strtoupper($notice3) );
header("Content-type: image/png"); 
$insert_x = imagesx($insert); 
$insert_y = imagesy($insert); 
//imagecopymerge($canvas,$insert,0,0,0,0,$insert_x,$insert_y,100);
imagepng($canvas);
?>
