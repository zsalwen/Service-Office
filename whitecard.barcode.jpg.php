<?
include 'common.php';

function makeBarcode($cord){
// we need to open a barcode and save it to the server
	$cord = $cord.'%';
	$url = 'http://staff.mdwestserve.com/standard/barcode.php?barcode='.$cord.'&width=200&height=40';
    $timeout = 30;
    $curl = curl_init();
    curl_setopt ($curl, CURLOPT_URL, $url);
    curl_setopt ($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt ($curl, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $png = curl_exec ($curl);
    curl_close ($curl);
	$path = '/data/service/photos/'.str_replace('%','',$cord);
	$filename = $path.'/barcode.png';
	$urlname = 'http://mdwestserve.com/photographs/'.str_replace('%','',$cord).'/barcode.png';
// Let's make sure the file exists and is writable first.
			if (!file_exists($path)){
				mkdir ($path,0777);
			}
			if (file_exists($filename)){
				unlink ($filename);
			}
			touch ($filename);
if (is_writable($filename)) {
    // In our example we're opening $filename in append mode.
    // The file pointer is at the bottom of the file hence
    // that's where $somecontent will go when we fwrite() it.
    if (!$handle = fopen($filename, 'a')) {
         //echo "Cannot open file ($filename)";
         exit;
    }
    // Write $somecontent to our opened file.
    if (fwrite($handle, $png) === FALSE) {
        //echo "Cannot write to file ($filename)";
        exit;
    }
    fclose($handle);
} else {
    //echo "The file $filename is not writable";
}
	// done with the barcode
}
makeBarcode($_GET[cord]);

$stamp = imagecreatefrompng('/data/service/photos/'.$_GET[cord].'/barcode.png');
$marge_right = 0;
$marge_bottom = 250;
//$sx = imagesx($stamp);
//$sy = imagesy($stamp);
$sx = 200;
$sy = 40;
$canvas = imagecreate( 210, 600);
$white = imagecolorallocate( $canvas, 255, 255, 255 );
$black = imagecolorallocate( $canvas, 0, 0, 0 );
$font = "/fonts/verdana.ttf";
$size = "12";
$total = number_format($_GET[cost]+2.8+2.3,2);
if (!$_GET[noCost]){
imageTTFText( $canvas, $size, 270, 195, 85, $black, $font, '$'.$_GET[cost] );
imageTTFText( $canvas, $size, 270, 170, 85, $black, $font, '$2.80' );
imageTTFText( $canvas, $size, 270, 145, 85, $black, $font, '$2.30' );
imageTTFText( $canvas, $size, 270, 120, 85, $black, $font, '$'.$total );
}
//imageTTFText( $canvas, $size, 270, 100, 85, $black, $font, '$4.90' );
imageTTFText( $canvas, $size, 270, 68, 10, $black, $font, $_GET[cord].': '.$_GET[name] );
imageTTFText( $canvas, $size, 270, 48, 10, $black, $font, $_GET[line1].', '.$_GET[line2]);
imageTTFText( $canvas, $size, 270, 25, 10, $black, $font, $_GET[csz] );
imagecopy($canvas, $stamp, imagesx($canvas) - $sx - $marge_right, imagesy($canvas) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
header("Content-type: image/jpeg"); 
imagejpeg( $canvas );
?>
