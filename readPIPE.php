<?php
error_log("[".date('g:iA n/j/y')."] [readPIPE] [start] \n", 3, '/logs/fail.log');
  $pipe="/tmp/pipe";
   if(!file_exists($pipe)) {
      echo "I am not blocked!";
   }
   else {
      //block and read from the pipe
      $f = fopen($pipe,"r");
      echo fread($f,10);
   }
error_log("[".date('g:iA n/j/y')."] [readPIPE] [end] \n", 3, '/logs/fail.log');
?>
