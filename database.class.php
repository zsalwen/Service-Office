<?
class database{
	var $database;
	function connect(){
		mysql_connect();
		mysql_select_db($this->database);
	}
}
?>