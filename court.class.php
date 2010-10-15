<?

class status{
	
	function counter(){
		echo "<table>";
		$r=@mysql_query("select distinct status, county from watchDog order by county, status");
		while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
			$county = addslashes($d[county]);
			$l=@mysql_query("select watchID from watchDog where status = '$d[status]' and county = '$county'");
			$count=mysql_num_rows($l);
			echo "<tr><td>".strtoupper($d[county])."</td><td>$d[status]</td><td>$count</td></tr>";
		}
		echo "</table>";
	}

	


}

?>
