<?
include 'database.class.php';
include 'court.class.php';

$db = new database;
$db -> database = 'core';
$db -> connect();

$system = new status;
$system -> counter();




?>