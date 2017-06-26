<?php
	
	define('DB', 'agoranet_itigvc');
	// define('DB', 'agoranet_ieag');
	define("TC", 3);
	define("pb", '/Public/');
	setlocale(LC_TIME, 'es_CO.UTF-8');
	
	require_once 'vendor/autoload.php';

	if(empty($_GET['url'])){
	    $url = "";
	}else{
	    $url = $_GET['url'];
	}

	$request = new App\Config\Request($url);
	$request->execute();
?>