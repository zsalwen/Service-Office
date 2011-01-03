<?php
$pipe="fifo";

if (! $handle=@fopen($pipe,"r") ) {
   echo "can't open pipe for reading\n";
   exit;
}


while (1) {

    $bytes=fread($handle,1);
    if ( strlen($bytes) == 0 ) 
       break;

    echo $bytes;
}

fclose($handle);
?>
