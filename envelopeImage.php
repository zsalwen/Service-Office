<?
include 'common.php';
$canvas = imagecreate( 380, 800);
$_SESSION[pass]=$_GET[line2];
$src = "http://service.mdwestserve.com/barcode.php";
$insert = imagecreatefrompng($src);
imagecolortransparent($insert,imagecolorat($insert,0,0));
$white = imagecolorallocate( $canvas, 255, 255, 255 );
$black = imagecolorallocate( $canvas, 0, 0, 0 );


$r=@mysql_query("SELECT * FROM envelopeImage WHERE envID = '$_GET[id]'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);

$font = "/fonts/OCRA.ttf";
$font2 = "/fonts/verdana.ttf";
$font3 = "/fonts/ATOMICCLOCKRADIO.TTF";
$size = "12";
$size2 = "22";
$size3 = "10";



//return address
$line1 = "__________________________"; 
//$line4 = 'HTTP://MDWestServe.com';
$line2 = '__________________________';
$line3 = '__________________________';
imageTTFText( $canvas, $size3, 270, 360, 0, $black, $font2, $line1);
imageTTFText( $canvas, $size3, 270, 345, 0, $black, $font2, $line2);
imageTTFText( $canvas, $size3, 270, 330, 0, $black, $font2, $line3);
$cord = "$clientFile".'X';
//main label

imageTTFText( $canvas, $size, 270, 220, 300, $black, $font, strtoupper($d[to1]) );
imageTTFText( $canvas, $size, 270, 200, 300, $black, $font, strtoupper($d[to2]) );
imageTTFText( $canvas, $size, 270, 180, 300, $black, $font, strtoupper($d[to3]) );

imageTTFText( $canvas, $size2, 270, 142, 50, $black, $font3, strtoupper($d[notice1]) );
imageTTFText( $canvas, $size2, 270, 112, 70, $black, $font3, strtoupper($d[notice2]) );
imageTTFText( $canvas, $size2, 270, 80, 240, $black, $font3, strtoupper($d[notice3]) );
header("Content-type: image/png"); 
$insert_x = imagesx($insert); 
$insert_y = imagesy($insert); 
//imagecopymerge($canvas,$insert,0,0,0,0,$insert_x,$insert_y,100);
imagepng($canvas);
?>
