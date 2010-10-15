<?
mysql_connect();
mysql_select_db('core');

@mysql_query("UPDATE dictionary SET wordCount = '0'"); // clear before each usage


function cleanWord($word){
	$word = trim($word);
	$word = strtoupper($word);
	$word = str_replace('.','',$word);	
	$word = str_replace(',','',$word);	
	$word = str_replace(':','',$word);	
	return $word;
}
function word($word){
	$word = cleanWord($word);
	$r=mysql_query("SELECT id, wordCount FROM dictionary WHERE wordText = '$word' ");
	if($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$counter = $d[wordCount] + 1;
		@mysql_query("UPDATE dictionary SET wordCount = '$counter' where id = '$d[id]'");
	}else{
		@mysql_query("INSERT INTO dictionary (wordText,wordCount) values ('$word','1')");
	}
	return "<li>$word: <b>$counter</b></li>";
}

function para($para){ 
	$words = explode(' ',$para);
	$count = count($words);
	$i=0;
	while ($i < $count){
		echo word($words[$i]);
	$i++;
	}
}

$r=@mysql_query("select action_str from ps_history");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
para($d[action_str]);
}



?>