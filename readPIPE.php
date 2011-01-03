<?php
error_log("[".date('g:iA n/j/y')."] [readPIPE] [start] \n", 3, '/logs/fail.log');
$pipe="/data/fifo";



if (! $handle=fopen($pipe,"r") ) {
   error_log("[".date('g:iA n/j/y')."] [readPIPE] [fopen failed] \n", 3, '/logs/fail.log');
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
error_log("[".date('g:iA n/j/y')."] [readPIPE] [end] \n", 3, '/logs/fail.log');

?>
