<?php
error_log("[".date('g:iA n/j/y')."] [readPIPE] [start] \n", 3, '/logs/fail.log');
  $pipe="/tmp/pipe";
   if(!file_exists($pipe)) {
      echo "<small>stale pipe</small>";
      $f = fopen($pipe,"r");
$data = fread($f,10);
error_log("[".date('g:iA n/j/y')."] [readPIPE] [stale pipe] [$data] \n", 3, '/logs/fail.log');
   }
   else {
      //block and read from the pipe
echo "<small>live pipe</small>";
      $f = fopen($pipe,"r");
$data = fread($f,10);
error_log("[".date('g:iA n/j/y')."] [readPIPE] [live pipe] [$data] \n", 3, '/logs/fail.log');
   }
error_log("[".date('g:iA n/j/y')."] [readPIPE] [end] \n", 3, '/logs/fail.log');
      echo $data;
?>
