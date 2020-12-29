<?php 
	
	$server_name = "localhost";
	$db_name = "sample_api";
	$user = "root";
	$password = "";

	$pdo = new PDO("mysql:host=$server_name; dbname=$db_name", $user, $password);
	try {

		$conn = $pdo;
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//echo "Connection successfully";
		
	} catch (Exception $e) {
		//echo "Connection Failed: ". $e->getMessage();
	}
?>