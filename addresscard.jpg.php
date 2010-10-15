<?
session_start();
include 'functions.php';
mysql_connect();
mysql_select_db('core');
//$id=$_GET[id];
//$q="SELECT * FROM ps_users where id = '$id'";
//$r=@mysql_query($q) or die(mysql_error());
//$d=mysql_fetch_array($r, MYSQL_ASSOC);
//if ($d[company]){$payTo = $d[company];}else{$payTo = $d[name];}
$line1 = "MD West Serve"; 
//if ($d[company]){
$line2 = 'HTTP://MDWestServe.com'; //}else{ $line2 = $d[name];}
$line3 = '300 E JOPPA RD STE 1102';
$line4 = 'TOWSON MD  21286-3012 ';
$canvas = imagecreate( 100, 600);
$white = imagecolorallocate( $canvas, 255, 255, 255 );
$black = imagecolorallocate( $canvas, 0, 0, 0 );
$font = "/fonts/verdana.ttf";
$size = "12";
imageTTFText( $canvas, $size, 270, 70, 70, $black, $font, $line1 );
imageTTFText( $canvas, $size, 270, 50, 70, $black, $font, $line2 );
imageTTFText( $canvas, $size, 270, 30, 70, $black, $font, $line3 );
imageTTFText( $canvas, $size, 270, 10, 70, $black, $font, $line4 );
header("Content-type: image/jpeg"); 
imagejpeg( $canvas );
?>
