<?php
$pipe="/tmp/pipe";
if(!file_exists($pipe)) {
 die();
}else {
 $f = fopen($pipe,"r");
 $data = fread($f,10);
}
echo $data;
?>
