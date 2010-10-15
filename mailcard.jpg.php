<?
include 'common.php';
//$id=$_GET[id];
//$q="SELECT * FROM ps_users where id = '$id'";
//$r=@mysql_query($q) or die(mysql_error());
//$d=mysql_fetch_array($r, MYSQL_ASSOC);
//if ($d[company]){$payTo = $d[company];}else{$payTo = $d[name];}
$line1 = "MD West Serve"; 
//if ($d[company]){
$line4 = 'HTTP://MDWestServe.com'; //}else{ $line2 = $d[name];}
$line2 = '300 E JOPPA RD STE 1103';
$line3 = 'TOWSON MD  21286-3012 ';
$canvas = imagecreate( 230, 100);
$white = imagecolorallocate( $canvas, 255, 255, 255 );
$black = imagecolorallocate( $canvas, 0, 0, 0 );
$font = "/fonts/verdana.ttf";
$size = "12";
imageTTFText( $canvas, $size, 0, 10, 20, $black, $font, $line1 );
imageTTFText( $canvas, $size, 0, 10, 40, $black, $font, $line2 );
imageTTFText( $canvas, $size, 0, 10, 60, $black, $font, $line3 );
imageTTFText( $canvas, $size, 0, 10, 80, $black, $font, $line4 );
header("Content-type: image/jpeg"); 
imagejpeg( $canvas );
?>