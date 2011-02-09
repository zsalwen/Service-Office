<?php
$path = "/sandbox/tmp";
if (!file_exists($path)){
		mkdir ($path,0777);
}
$path = $path."/bwa.csv"; 
if(move_uploaded_file($_FILES['csv']['tmp_name'], $path)) {
    echo $_FILES['csv']['name']." recieved.<br>";
}
if (file_exists('/sandbox/tmp/bwa.csv')){
session_start();
$row = 1;
$handle = fopen("/sandbox/tmp/bwa.csv", "r");
mysql_connect();
mysql_select_db('service');
function leading_zeros($value, $places){
    if(is_numeric($value)){
        for($x = 1; $x <= $places; $x++){
            $ceiling = pow(10, $x);
            if($value < $ceiling){
                $zeros = $places - $x;
                for($y = 1; $y <= $zeros; $y++){
                    $leading .= "0";
                }
            $x = $places + 1;
            }
        }
        $output = $leading . $value;
    }
    else{
        $output = $value;
    }
    return $output;
}
function needLeading($str){
if (strlen($str) != 2){ return leading_zeros($str,2); }
else{ return $str; }
}

function processDetails($check,$amount){
	if ($check != 'Serial'){
		echo "<li>$check for $amount</li>";
		@mysql_query("update bwa set rateTypeA ='$amount' where zip ='$check')");
	}
}

ob_start();
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	processDetails($data[2],$data[6]);
}
fclose($handle);
$buffer = ob_get_clean();

}

?>
<div>Upload CSV For Processing<br>
<form enctype="multipart/form-data" action="loadBWA.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
Choose a csv file to upload: <input name="csv" type="file" />
<input type="submit" value="Run Test" />
</form>
</div>
<ol>
<?=$buffer;?>
</ol>
